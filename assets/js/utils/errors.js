
export const emptyErrors = () => {
    return {
        message: '',
        fields: {}
    }
}

export const parseResponseError = (error) => {
    let errors = emptyErrors()
    const data = error.response?.data
    if (data.title) {
        errors.message = data.title
    } else if (error.message) {
        errors.message = error.message
    }
    const violations = data?.violations
    if (violations) {
        for (let violation of violations) {
            errors.fields[violation.propertyPath] = violation.title
        }
    }
    return errors
}