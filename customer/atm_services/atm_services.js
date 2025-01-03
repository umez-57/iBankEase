// Function to show withdraw input field
function showWithdraw() {
    const remainingLimit = parseFloat(document.getElementById('remaining-limit').textContent);

    if (remainingLimit > 0) {
        document.getElementById('withdraw-field').style.display = 'block';
        document.getElementById('deposit-field').style.display = 'none';
        document.getElementById('success-message').style.display = 'none';
        document.getElementById('error-message').style.display = 'none';
    } else {
        showError("Withdrawal not available. Limit exceeded. Try again after 24 hours.");
    }
}

// Function to show deposit input field
function showDeposit() {
    document.getElementById('deposit-field').style.display = 'block';
    document.getElementById('withdraw-field').style.display = 'none';
    document.getElementById('success-message').style.display = 'none';
    document.getElementById('error-message').style.display = 'none';
}

// Function to withdraw the amount with client-side check
function withdrawAmount() {
    let withdrawAmount = parseFloat(document.getElementById('withdraw-amount').value);
    let availableBalance = parseFloat(document.getElementById('available-balance').textContent);
    let remainingLimit = parseFloat(document.getElementById('remaining-limit').textContent);

    // Client-side check before making the request
    if (isNaN(withdrawAmount) || withdrawAmount <= 0) {
        showError('Please enter a valid withdrawal amount.');
    } else if (withdrawAmount > remainingLimit) {
        showError('Withdrawal amount exceeds your remaining daily limit.');
    } else if (withdrawAmount > availableBalance) {
        showError('Insufficient balance for withdrawal.');
    } else {
        // If checks pass, proceed with the transaction
        updateBalanceAndLimitInDB(withdrawAmount, 'Withdraw');
    }
}

// Function to deposit the amount with client-side check
function depositAmount() {
    let depositAmount = parseFloat(document.getElementById('deposit-amount').value);

    // Client-side validation
    if (isNaN(depositAmount) || depositAmount <= 0) {
        showError('Please enter a valid deposit amount.');
        return;
    }

    // If valid, proceed with the transaction
    updateBalanceAndLimitInDB(depositAmount, 'Deposit');
}

// Function to update balance and limit in the database via AJAX
function updateBalanceAndLimitInDB(amount, type) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "atm_transaction.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    let data = `amount=${amount}&transaction_type=${type}`;
    xhr.send(data);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                // Update the displayed balance
                document.getElementById('available-balance').textContent = response.balance.toFixed(2);

                // Update remaining limit only for withdrawal
                if (type === 'Withdraw') {
                    document.getElementById('remaining-limit').textContent = response.remaining_limit.toFixed(2);
                }

                // Display success message
                let successMessage = `${type} of ₹${amount.toFixed(2)} was successful!`;
                document.getElementById('success-message').textContent = successMessage;
                document.getElementById('success-message').style.display = 'block';
                document.getElementById('error-message').style.display = 'none';

                // Check if remaining limit reached zero and disable Withdraw button if true
                if (type === 'Withdraw' && response.remaining_limit <= 0) {
                    disableWithdrawButton();
                }
            } else {
                // Display error message from the response
                showError(response.message);
            }
        }
    };
}

// Function to disable the withdraw button
function disableWithdrawButton() {
    const withdrawBtn = document.getElementById('withdraw-btn');
    withdrawBtn.disabled = true;
    withdrawBtn.classList.add('disabled');
    withdrawBtn.setAttribute('title', 'Withdrawal not available, try after 24 hours');
}

// Function to display error message
function showError(message) {
    document.getElementById('error-message').textContent = message;
    document.getElementById('error-message').style.display = 'block';
    document.getElementById('success-message').style.display = 'none';
}
