const reader = new Html5Qrcode("reader");

reader.start(
  { facingMode: "environment" },
  { fps: 10, qrbox: { width: 250, height: 250 } },
  qrCodeMessage => {
    
    fetch("api/attendance.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ qrData: qrCodeMessage })
    })
      .then(res => res.json())
      .then(data => {
        const resultBox = document.getElementById("result");
        resultBox.innerHTML = data.message;
        resultBox.style.color = data.message.includes("marked") ? "green" : "red";

       
        fetch("api/get_attendance_today.php")
          .then(r => r.text())
          .then(html => {
            document.getElementById("attendanceTableBody").innerHTML = html;
          });

        setTimeout(() => {
          resultBox.innerHTML = "";
        }, 3000);
      })
      .catch(err => {
        document.getElementById("result").innerHTML = "Error: " + err.message;
        document.getElementById("result").style.color = "red";
      });
  },
  errorMessage => {
  }
);
