// Example script for basic form validation
function validateForm() {
    const inputs = document.querySelectorAll('input, textarea');
    for (let input of inputs) {
        if (!input.value) {
            alert('Please fill out all fields!');
            return false;
        }
    }
    return true;
}
