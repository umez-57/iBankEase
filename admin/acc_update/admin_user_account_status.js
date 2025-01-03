const branchDetails = {
    'delhi': { 'address': 'Connaught Place, Delhi', 'ifsc': 'SBIN0000456' },
    'mumbai': { 'address': 'Nariman Point, Mumbai', 'ifsc': 'SBIN0001234' },
    'bangalore': { 'address': 'MG Road, Bangalore', 'ifsc': 'SBIN0000789' }
};

function fetchUsersByBranch() {
    const branch = document.getElementById("branchSelect").value;
    if (branch === "") {
        document.getElementById("userTableContainer").style.display = "none";
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "fetch_users_by_branch_account_status.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            const userTableBody = document.getElementById("userTableBody");
            userTableBody.innerHTML = "";

            if (response.status === "success") {
                response.users.forEach(user => {
                    const isBlocked = user.is_blocked === 'yes';
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${user.username}</td>
                        <td>${user.fullname}</td>
                        <td>${user.balance || '0.00'}</td>
                        <td>${isBlocked ? 'Blocked' : 'Unblocked'}</td>
                        <td>
                            <button 
                                onclick="toggleAccountStatus('${user.username}', '${isBlocked ? 'no' : 'yes'}')" 
                                class="${isBlocked ? 'unblock-button' : 'block-button'}">
                                ${isBlocked ? 'Unblock' : 'Block'}
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

function toggleAccountStatus(username, newStatus) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update_account_status.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === "success") {
                showMessage(`Account ${newStatus === 'yes' ? 'blocked' : 'unblocked'} successfully!`, "success");
                fetchUsersByBranch(); // Refresh the user list
            } else {
                showMessage(response.message || "Failed to update account status.", "error");
            }
        } else {
            showMessage("Failed to update account status.", "error");
        }
    };

    xhr.onerror = function () {
        showMessage("Network error while updating account status.", "error");
    };

    xhr.send(`username=${encodeURIComponent(username)}&status=${encodeURIComponent(newStatus)}`);
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
