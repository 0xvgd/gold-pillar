
const uploadUrl = `${window.baseUrl}upload`

export const upload = (blob, onload, filename) => {
    const formData = new FormData()
    formData.append('file', blob, filename)
    const req = new XMLHttpRequest();
    req.open('POST', uploadUrl)
    req.onload = (event) => {
        if (req.status == 200) {
            const result = JSON.parse(req.responseText)
            onload(result, event)
        } else {
            alert('Erro ao fazer upload')
        }
    }
    req.send(formData)
}