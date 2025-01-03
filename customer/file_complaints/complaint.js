document.getElementById('complaintForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent form from submitting normally

    const title = document.getElementById('title').value;
    const category = document.getElementById('category').value;
    const description = document.getElementById('description').value;
    const messageDiv = document.getElementById('message');

    // Prepare data for the AJAX request
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "process_complaint.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                messageDiv.textContent = response.message;
                messageDiv.className = 'message-success';
                // Clear the form fields after successful submission
                document.getElementById('complaintForm').reset();
            } else {
                messageDiv.textContent = response.message;
                messageDiv.className = 'message-error';
            }
        } else {
            messageDiv.textContent = 'An error occurred. Please try again.';
            messageDiv.className = 'message-error';
        }
    };

    xhr.send(`title=${encodeURIComponent(title)}&category=${encodeURIComponent(category)}&description=${encodeURIComponent(description)}`);
});
