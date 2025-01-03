function showDepositField(username) {
    // Hide any other open deposit forms
    document.querySelectorAll('.deposit-form').forEach(form => form.style.display = 'none');
    
    // Show deposit form for the selected user
    const depositForm = document.getElementById(`deposit-form-${username}`);
    if (depositForm) {
        depositForm.style.display = 'block';
    }
}

function depositAmount(username) {
    const amountInput = document.getElementById(`deposit-amount-${username}`);
    const amount = parseFloat(amountInput.value);
    
    // Validate the entered amount
    if (isNaN(amount) || amount <= 0) {
        showMessage('Please enter a valid amount greater than 0.', 'error');
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "process_deposit.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        if (xhr.status === 200) {
            let response;
            try {
                response = JSON.parse(xhr.responseText);
            } catch (e) {
                showMessage('An error occurred. Invalid server response.', 'error');
                return;
            }

            if (response.status === 'success') {
                // Update the displayed balance in the table
                const balanceCell = document.querySelector(`#row-${username} .balance`);
                if (balanceCell) {
                    balanceCell.textContent = `₹${parseFloat(response.new_balance).toFixed(2)}`;
                }
                showMessage(response.message, 'success');
                amountInput.value = ''; // Clear the amount field after successful deposit
            } else {
                showMessage(response.message || 'An error occurred. Please try again.', 'error');
            }
        } else {
            showMessage('An error occurred. Please try again.', 'error');
        }
    };

    xhr.onerror = function() {
        showMessage('Request failed. Please check your network connection.', 'error');
    };

    xhr.send(`username=${encodeURIComponent(username)}&amount=${encodeURIComponent(amount)}`);
}

function showMessage(message, type) {
    const messageDiv = document.getElementById('message');
    messageDiv.textContent = message;
    messageDiv.className = type === 'success' ? 'message-success' : 'message-error';
    messageDiv.style.display = 'block';

    // Automatically hide the message after a few seconds
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 5000);
}
