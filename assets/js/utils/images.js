import 'cropperjs/dist/cropper.min.css'
import Cropper from 'cropperjs/dist/cropper.common'
import { upload as fileUpload } from './files'

let cropper = null
let onCropCallback = null
let aspectRatio = null
const image = document.getElementById('pictureToCrop')
const $cropperModal = $('#cropperModal')

$cropperModal
    .on('shown.bs.modal', () => {
        cropper = new Cropper(image, {
            viewMode: 1,
            autoCropArea: 1,
            aspectRatio
        })
    })
    .on('hidden.bs.modal', function() {
        const canvas = cropper.getCroppedCanvas({
            maxWidth: 1280
        });

        if (onCropCallback) {
            onCropCallback(canvas)
        }

        cropper.destroy()
        cropper = null
    })

export const cropImage = (input, oncrop) => {
    if (input.files && input.files[0]) {
        const file = input.files[0]
        const reader = new FileReader()
        reader.onload = (e) => {
            onCropCallback = oncrop
            image.src = e.target.result
            aspectRatio = parseFloat(input.dataset.aspectRatio)
            $cropperModal.modal('show');
        }
        reader.readAsDataURL(file)
    }
}

export const cropImageAndUpload = (input, onupload) => {
    cropImage(input, (canvas) => {
        canvas.toBlob((blob) => {
            const filename = input.files[0].name
            fileUpload(blob, onupload, filename)
        }, input.files[0].type)
    })
}