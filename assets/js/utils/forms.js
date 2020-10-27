
export const ajaxSubmit = (selector, props) => {
    $(selector).on('submit', (e) => {
        e.preventDefault()

        const $form = $(e.target)
        const $error = $form.find('.alert.alert-danger').hide()
        const $success = $form.find('.alert.alert-success').hide()

        $.ajax({
            url: $form.attr('action'),
            method: $form.attr('method'),
            data: $form.serialize(),
            success(response) {
                $success
                .text(response.message)
                .show()
                $form.trigger('reset')

                if (props.success) {
                    props.success.call($form, response)
                }
            },
            error(response) {
                let $message = '';
                if (response.responseJSON.message) {
                    $message = $(`<p>${response.responseJSON.message}</p>`)
                }
                let $list = $('<ul></ul>')
                for (let error of response.responseJSON.errors) {
                    $list.append(`<li>${error.propertyPath}: ${error.message}</li>`)
                }
                $error
                .html('')
                .append($message)
                .append($list)
                .show()

                if (props.error) {
                    props.error.call($form, response.responseJSON)
                }
            },
            complete() {
                $form.find('[type=submit]').prop('disabled', false)
            }
        })
    })
}