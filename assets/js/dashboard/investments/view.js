
$transactionForm = $('#transaction-form')

$transactionForm.on('submit', () => {
    const agree = confirm('Confirm? This action cannot be undone.')
    return agree;
})