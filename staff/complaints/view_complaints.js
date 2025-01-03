// Function to load complaints for the selected customer
function loadComplaints(username) {
    if (username === "") {
        const complaintTable = document.getElementById("complaintTable");
        const noComplaints = document.getElementById("noComplaints");

        if (complaintTable) complaintTable.style.display = "none";
        if (noComplaints) noComplaints.style.display = "none";
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "fetch_complaints.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        const complaintTable = document.getElementById("complaintTable");
        const complaintBody = document.getElementById("complaintBody");
        const noComplaints = document.getElementById("noComplaints");

        console.log("Server response:", xhr.responseText);

        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);

                if (response.status === "success" && response.complaints.length > 0) {
                    if (complaintBody) complaintBody.innerHTML = "";

                    // Display each complaint as a row in the table
                    response.complaints.forEach(complaint => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${complaint.complaint_id}</td>
                            <td>${complaint.title}</td>
                            <td class="${complaint.status === 'Pending' ? 'pending' : 'resolved'}">${complaint.status}</td>
                            <td><button onclick="viewComplaintDetails(${complaint.complaint_id})">View Complaint</button></td>
                        `;
                        if (complaintBody) complaintBody.appendChild(row);
                    });

                    if (complaintTable) complaintTable.style.display = "block";
                    if (noComplaints) noComplaints.style.display = "none";
                } else {
                    console.log("No complaints found for user.");

                    if (complaintTable) complaintTable.style.display = "none";
                    if (noComplaints) noComplaints.style.display = "block";
                }
            } catch (error) {
                console.error("Failed to parse JSON:", error);
                console.log("Response was:", xhr.responseText);
            }
        } else {
            console.error("Failed to fetch complaints with status", xhr.status);
        }
    };

    xhr.onerror = function() {
        console.error("Network error while fetching complaints.");
    };

    xhr.send(`username=${encodeURIComponent(username)}`);
}

// Function to display complaint details
function viewComplaintDetails(complaintId) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "fetch_complaint_details.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        const detailsDiv = document.getElementById("complaintDetails");

        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.status === "success" && detailsDiv) {
                    const complaint = response.complaint;

                    detailsDiv.innerHTML = `
                        <h3>Complaint Details</h3>
                        <p><strong>Title:</strong> ${complaint.title}</p>
                        <p><strong>Category:</strong> ${complaint.category}</p>
                        <p><strong>Description:</strong> ${complaint.description}</p>
                        <p><strong>Status:</strong> ${complaint.status}</p>
                        <p><strong>Date Filed:</strong> ${complaint.created_at}</p>
                        <button onclick="updateComplaintStatus(${complaintId}, 'Pending')">Mark as Pending</button>
                        <button onclick="updateComplaintStatus(${complaintId}, 'Resolved')">Mark as Resolved</button>
                    `;
                    detailsDiv.style.display = "block";
                } else {
                    showMessage("Failed to fetch complaint details: " + response.message, "error");
                }
            } catch (error) {
                console.error("Error parsing response for complaint details:", error);
                console.log("Raw response:", xhr.responseText);
                showMessage("Failed to fetch complaint details due to parsing error.", "error");
            }
        } else {
            showMessage("Failed to fetch complaint details.", "error");
        }
    };

    xhr.onerror = function() {
        showMessage("Failed to fetch complaint details due to a network error.", "error");
    };

    xhr.send(`complaint_id=${encodeURIComponent(complaintId)}`);
}

// Function to update complaint status
function updateComplaintStatus(complaintId, status) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update_complaint_status.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        const messageDiv = document.getElementById("message");
        
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.status === "success") {
                    loadComplaints(document.querySelector("select").value); // Refresh complaints
                    const detailsDiv = document.getElementById("complaintDetails");
                    if (detailsDiv) detailsDiv.style.display = "none";
                    showMessage(`Complaint marked as ${status}.`, "success");
                } else {
                    showMessage("Failed to update complaint status.", "error");
                }
            } catch (error) {
                console.error("Error parsing response for update:", error);
                showMessage("Failed to update complaint status due to parsing error.", "error");
            }
        }
    };

    xhr.send(`complaint_id=${encodeURIComponent(complaintId)}&status=${encodeURIComponent(status)}`);
}

// Function to display messages
function showMessage(message, type) {
    const messageDiv = document.getElementById("message");
    if (messageDiv) {
        messageDiv.textContent = message;
        messageDiv.className = type === "success" ? "message-success" : "message-error";
        messageDiv.style.display = "block";

        setTimeout(() => {
            messageDiv.style.display = "none";
        }, 5000);
    }
}
