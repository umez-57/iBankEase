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
    xhr.open("POST", "fetch_users_by_branch.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            const userTableBody = document.getElementById("userTableBody");
            userTableBody.innerHTML = "";

            if (response.status === "success") {
                response.users.forEach(user => {
                    const row = document.createElement("tr");
                    row.setAttribute("data-id", user.id);  // Ensure each row has data-id for targeting
                    row.innerHTML = `
                        <td>${user.id}</td>
                        <td>${user.username}</td>
                        <td>${user.password}</td>
                        <td>${user.fullname}</td>
                        <td>${user.email}</td>
                        <td>${user.phone}</td>
                        <td>${user.branch_address}</td>
                        <td><button onclick="showUpdateSection(${user.id})">Change Details</button></td>
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

function showUpdateSection(userId) {
    const row = document.querySelector(`tr[data-id="${userId}"]`);
    if (!row) {
        console.error(`Row with userId ${userId} not found.`);
        return;
    }

    let updateSection = row.nextElementSibling;
    
    // Check if the update section already exists; if not, create it
    if (!updateSection || !updateSection.classList.contains("update-section")) {
        updateSection = document.createElement("tr");
        updateSection.classList.add("update-section");
        updateSection.innerHTML = `
            <td colspan="8">
                <h4>Updating Details for User ID ${userId}</h4>
                <label>Full Name:</label><input type="text" id="fullname_${userId}" value="${row.children[3].textContent.trim()}">
                <label>Password:</label><input type="text" id="password_${userId}">
                <label>Email:</label><input type="email" id="email_${userId}">
                <label>Phone:</label><input type="tel" id="phone_${userId}">
                <label>Branch Address:</label>
                <select id="branch_${userId}">
                    <option value="delhi">Delhi</option>
                    <option value="mumbai">Mumbai</option>
                    <option value="bangalore">Bangalore</option>
                </select>
                <button onclick="updateUser(${userId})">Confirm Changes</button>
            </td>
        `;
        row.after(updateSection);
    }
}

function updateUser(userId) {
    const fullname = document.getElementById(`fullname_${userId}`).value.trim();
    const password = document.getElementById(`password_${userId}`).value.trim();
    const email = document.getElementById(`email_${userId}`).value.trim();
    const phone = document.getElementById(`phone_${userId}`).value.trim();
    const branch = document.getElementById(`branch_${userId}`).value;

    // Check if all fields are filled
    if (!fullname || !password || !email || !phone || !branch) {
        showMessage("Please fill in all fields before updating.", "error");
        return;
    }

    // Create the request parameters
    const params = `id=${encodeURIComponent(userId)}&fullname=${encodeURIComponent(fullname)}&password=${encodeURIComponent(password)}&email=${encodeURIComponent(email)}&phone=${encodeURIComponent(phone)}&branch=${encodeURIComponent(branch)}`;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update_user_details.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        try {
            const response = JSON.parse(xhr.responseText);
            if (xhr.status === 200 && response.status === "success") {
                showMessage("User updated successfully!", "success");
                fetchUsersByBranch(); // Refresh the list
            } else {
                showMessage(response.message || "Failed to update user.", "error");
            }
        } catch (e) {
            console.error("Error parsing response:", xhr.responseText);
            showMessage("Failed to update user due to server error.", "error");
        }
    };

    xhr.onerror = function () {
        showMessage("Network error while updating user.", "error");
    };

    xhr.send(params);
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
