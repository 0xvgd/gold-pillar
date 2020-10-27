import 'jodit/build/jodit.min.css'
import 'cropperjs/dist/cropper.min.css'
import { cropImageAndUpload } from '../utils/images'

(function () {
    'use strict'

    const applyCropperToInput = () => {
        const inputs = document.querySelectorAll('input[data-preview]')
        inputs.forEach(input => {
            if (input.attributes.initialized) {
                return
            }
            input.attributes.initialized = true
            input.onchange = () => {
                const loading = document.querySelector(input.dataset.loading)
                $(loading).show()
                cropImageAndUpload(input, (result, event) => {
                    if (result.response && result.response.files && result.response.files.length) {
                        const url = result.response.files[0].url
                        const preview = document.querySelector(input.dataset.preview)
                        const target = document.querySelector(input.dataset.target)
                        if (preview) {
                            preview.src = url
                        }
                        if (target) {
                            target.value = url
                        }
                        $(loading).hide()
                    }
                })
            }
        })
    }

    $('[data-prototype]').collection({
        onadd() {
            applyCropperToInput()
        }
    });
    applyCropperToInput()
   
})();

