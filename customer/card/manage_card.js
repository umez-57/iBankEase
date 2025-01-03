let cvvVisible = false;

// Show/Hide CVV
function showCVV(cvv) {
    const cvvHiddenElement = document.getElementById('cvv-hidden');
    const cvvButton = document.getElementById('cvv-toggle');
    
    if (!cvvVisible) {
        // Show CVV
        cvvHiddenElement.innerHTML = cvv;
        cvvButton.innerHTML = 'Hide';
        cvvVisible = true;
    } else {
        // Hide CVV
        cvvHiddenElement.innerHTML = '•••';
        cvvButton.innerHTML = 'Show';
        cvvVisible = false;
    }
}

// Block or Unblock card
function blockUnblockCard(cardNumber, action) {
    fetch('manage_card_backend.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `card_number=${cardNumber}&action=${action}`
    })
    .then(response => response.json())
    .then(data => {
        const successMessage = document.getElementById('success-message');
        if (data.status === 'success') {
            if (action === 'block') {
                successMessage.innerHTML = "Card blocked successfully!";
                successMessage.style.color = 'red';
            } else {
                successMessage.innerHTML = "Card unblocked successfully!";
                successMessage.style.color = 'green';
            }
        } else {
            successMessage.innerHTML = data.message;
            successMessage.style.color = 'red';
        }
        successMessage.style.display = 'block';
    });
}

// Change daily limit
function changeDailyLimit(cardNumber) {
    const newLimit = document.getElementById('new-daily-limit').value;
    
    if (newLimit === "") {
        alert("Please enter a new daily limit.");
        return;
    }
    
    fetch('manage_card_backend.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `card_number=${cardNumber}&action=change_limit&new_limit=${newLimit}`
    })
    .then(response => response.json())
    .then(data => {
        const successMessage = document.getElementById('success-message');
        if (data.status === 'success') {
            successMessage.innerHTML = "Daily limit updated successfully!";
            successMessage.style.color = 'green';
        } else {
            successMessage.innerHTML = data.message;
            successMessage.style.color = 'red';
        }
        successMessage.style.display = 'block';
    });
}

// Change PIN
function changePin(cardNumber) {
    const newPin = document.getElementById('new-pin').value;
    
    if (newPin.length !== 4) {
        alert("PIN must be 4 digits.");
        return;
    }
    
    fetch('manage_card_backend.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `card_number=${cardNumber}&action=change_pin&new_pin=${newPin}`
    })
    .then(response => response.json())
    .then(data => {
        const successMessage = document.getElementById('success-message');
        if (data.status === 'success') {
            successMessage.innerHTML = "PIN changed successfully!";
            successMessage.style.color = 'green';
        } else {
            successMessage.innerHTML = data.message;
            successMessage.style.color = 'red';
        }
        successMessage.style.display = 'block';
    });
}
