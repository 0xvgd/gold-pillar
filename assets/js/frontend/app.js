import '../../css/frontend.scss';
import { ajaxSubmit } from '../utils/forms'

$('#sign-in-modal form')
.on('sign-in-presubmit', (e) => {
    $(e.currentTarget).find('.alert.alert-danger').hide()
})
.on('sign-in-success', (e, response) => {
    window.location.reload()
})
.on('sign-in-error', (e, response) => {
    $(e.currentTarget)
        .find('.alert.alert-danger')
        .text(response.message || 'Unknown error')
        .show()
})
.on('submit', (e) => {
    e.preventDefault()

    const $form = $(e.target)
    $form.trigger('sign-in-presubmit')

    $.ajax({
        url: $form.attr('action'),
        method: $form.attr('method'),
        data: $form.serialize(),
        success(response) {
            if (response.success) {
                $form.trigger('sign-in-success', [response])
            } else {
                $form.trigger('sign-in-error', [response])
            }
        }
    })
})

ajaxSubmit('#book-a-view-modal form', {
    success() {
        this.find('.fields,[type=submit]').hide()
    }
})
ajaxSubmit('#make-an-offer-modal form', {
    success() {
        this.find('.fields,[type=submit]').hide()
    }
})

$('#book-a-view-modal').each((i, e) => {
    const $form = $(e)
    const $date = $('#' + $form.data('input-date-id'))
    const $time = $('#' + $form.data('input-time-id'))
    const baseUrl = $form.data('days-path')

    $date.on('change', () => {
        $time.html('<option value=""></option>')
        const date = $date.val()
        $.ajax({
            url: `${baseUrl}/${date}`,
            success(response) {
                for (let time of response) {
                    $time.append(`<option value="${time}">${time}</option>`)
                }
            }
        })
    })
})

$(document).ready(() => {
    $('.carousel').each((i, elem) => {
        let $first = null
        let $carousel = $(elem)
        $carousel.find("img[data-src]").each((i, img) => {
            let $lazy = $(img)
            if (!$first) {
                $first = $lazy
                $first.on('load', () => {
                    $carousel.carousel({
                        interval: 4000
                    })
                })
            }
            $lazy.attr("src", $lazy.data('src'));
            $lazy.removeAttr("data-src");
        })
    })

    $('img[data-src]').each((i, elem) => {
        let $lazy = $(elem)
        let image = new Image()
        image.src = $lazy.data('src')
        image.onload = () => {
            $lazy.attr("src", image.src);
            $lazy.removeAttr("data-src");
        }
    })

    $('[data-background]').each((i, elem) => {
        let $lazy = $(elem)
        let image = new Image()
        image.src = $lazy.data('src')
        image.onload = () => {
            $lazy.css("background-image", `url(${image.src})`);

            $lazy.removeAttr("data-background");
        }
    })
    
    $('.page-loader').animate({
        opacity: 0,
    }, 300, () => {
        $('.page-loader').remove()
    })
})

