function fetchUsersByBranch() {
    const branch = document.getElementById("branchSelect").value;
    if (branch === "") {
        document.getElementById("userTableContainer").style.display = "none";
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "fetch_users_with_cards.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            const userTableBody = document.getElementById("userTableBody");
            userTableBody.innerHTML = "";

            if (response.status === "success") {
                response.users.forEach(user => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${user.username}</td>
                        <td>${user.fullname}</td>
                        <td>${user.balance}</td>
                        <td>${user.card_status === 'yes' ? 'Blocked' : 'Unblocked'}</td>
                        <td>
                            <button 
                                onclick="toggleCardStatus('${user.account_number}', '${user.card_status}')" 
                                style="background-color: ${user.card_status === 'yes' ? 'green' : 'red'}; color: white;">
                                ${user.card_status === 'yes' ? 'Unblock' : 'Block'}
                            </button>
                        </td>
                    `;
                    userTableBody.appendChild(row);
                });

                document.getElementById("userTableContainer").style.display = "block";
            } else {
                showMessage(response.message, "error");
            }
        } else {
            showMessage("Failed to fetch users.", "error");
        }
    };
    xhr.send(`branch=${branch}`);
}

function toggleCardStatus(accountNumber, currentStatus) {
    const newStatus = currentStatus === 'yes' ? 'no' : 'yes';

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update_card_status.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        const response = JSON.parse(xhr.responseText);
        if (xhr.status === 200 && response.status === "success") {
            showMessage(`Card ${newStatus === 'yes' ? 'blocked' : 'unblocked'} successfully!`, "success");
            fetchUsersByBranch(); // Refresh the list
        } else {
            showMessage("Failed to update card status.", "error");
        }
    };
    xhr.send(`account_number=${accountNumber}&is_blocked=${newStatus}`);
}

function showMessage(message, type) {
    const messageDiv = document.getElementById("message");
    messageDiv.textContent = message;
    messageDiv.className = type === "success" ? "message-success" : "message-error";
    messageDiv.style.display = "block";

    setTimeout(() => {
        messageDiv.style.display = "none";
    }, 5000);
}
