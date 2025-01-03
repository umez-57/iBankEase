function fetchUsersByBranch() {
    const branch = document.getElementById("branchSelect").value;
    if (branch === "") {
        document.getElementById("userTableContainer").style.display = "none";
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "fetch_users_balance.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            const userTableBody = document.getElementById("userTableBody");
            userTableBody.innerHTML = "";

            if (response.status === "success") {
                response.users.forEach(user => {
                    const row = document.createElement("tr");
                    row.setAttribute("data-username", user.username);
                    row.innerHTML = `
                        <td>${user.username}</td>
                        <td>${user.fullname}</td>
                        <td>${user.balance}</td>
                        <td><button onclick="showUpdateBalanceSection('${user.username}')">Update</button></td>
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

function showUpdateBalanceSection(username) {
    const row = document.querySelector(`tr[data-username="${username}"]`);
    if (!row) {
        console.error(`Row with username ${username} not found.`);
        return;
    }

    let updateSection = row.nextElementSibling;

    if (!updateSection || !updateSection.classList.contains("update-section")) {
        updateSection = document.createElement("tr");
        updateSection.classList.add("update-section");
        updateSection.innerHTML = `
            <td colspan="4">
                <label>New Balance:</label>
                <input type="number" id="new_balance_${username}" min="0" step="0.01">
                <button onclick="updateBalance('${username}')">Update Balance</button>
            </td>
        `;
        row.after(updateSection);
    }
}

function updateBalance(username) {
    const newBalance = document.getElementById(`new_balance_${username}`).value.trim();

    if (newBalance === "") {
        showMessage("Please enter a balance amount.", "error");
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update_user_balance.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        try {
            const response = JSON.parse(xhr.responseText);
            if (xhr.status === 200 && response.status === "success") {
                showMessage("Balance updated successfully!", "success");
                fetchUsersByBranch(); // Refresh the table
            } else {
                showMessage(response.message || "Failed to update balance.", "error");
            }
        } catch (e) {
            console.error("Error parsing response:", xhr.responseText);
            showMessage("Failed to update balance due to server error.", "error");
        }
    };

    xhr.send(`username=${encodeURIComponent(username)}&new_balance=${encodeURIComponent(newBalance)}`);
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
