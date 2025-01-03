// Predefined IFSC codes with branch details
const ifscData = {
    "SBIN0000456": "Connaught Place, Delhi",
    "SBIN0001234": "Nariman Point, Mumbai",
    "SBIN0000789": "MG Road, Bangalore"
};

// Fetch available balance dynamically from PHP page
const availableBalance = parseFloat(document.getElementById('available-balance').textContent.replace(/[^0-9.-]+/g,""));

// Verify IFSC Code
function verifyIFSC() {
    const ifsc = document.getElementById('ifsc').value;
    const branchDetails = document.getElementById('branch-details');
    const ifscError = document.getElementById('ifsc-error');

    if (ifscData[ifsc]) {
        branchDetails.style.display = 'block';
        branchDetails.innerText = `Branch Address: ${ifscData[ifsc]}`;
        ifscError.style.display = 'none';
    } else {
        branchDetails.style.display = 'none';
        ifscError.style.display = 'block';
        ifscError.innerText = 'Invalid IFSC code!';
    }
}

// Verify Account Number (AJAX request to check if account exists)
function verifyAccount() {
    const accountNumber = document.getElementById('account-number').value;
    const accountName = document.getElementById('account-name');
    const accountError = document.getElementById('account-error');

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "verify_account.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        if (this.status === 200) {
            const response = JSON.parse(this.responseText);
            if (response.exists) {
                accountName.style.display = 'block';
                accountName.innerText = `Account Holder: ${response.name}`;
                accountError.style.display = 'none';
            } else {
                accountName.style.display = 'none';
                accountError.style.display = 'block';
                accountError.innerText = 'Account not found!';
            }
        }
    };

    xhr.send(`account_number=${accountNumber}`);
}

// Check amount validity (available balance and prevent self-transfer)
function checkAmount(event) {
    const transferAmount = parseFloat(document.getElementById('amount').value);
    const balanceError = document.getElementById('balance-error');
    const senderAccountNumber = "649536207622";  // Replace this with the sender's account number from the session
    const recipientAccountNumber = document.getElementById('account-number').value;

    // Prevent transfer to self
    if (senderAccountNumber === recipientAccountNumber) {
        balanceError.style.display = 'block';
        balanceError.innerText = "Cannot transfer to the same account!";
        event.preventDefault(); // Stop form submission
        return;
    }

    // Check for sufficient balance
    if (transferAmount > availableBalance) {
        balanceError.style.display = 'block';
        balanceError.innerText = "Insufficient balance!";
        event.preventDefault(); // Stop form submission
    } else {
        balanceError.style.display = 'none';
    }
}
