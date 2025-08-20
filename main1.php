<?php
session_start(); 

$host = "localhost";
$user = "root";
$password = "";
$database = "recommendation";

if (!isset($_SESSION['email'])) {
    header("Location: newlog.html"); // Redirect to login if not logged in
    exit();
}
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_SESSION["email"];
$sql = "SELECT name FROM register WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$name = $user ? $user["name"] : "User"; 
$stmt->close();
$conn->close();

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Health Recommendation</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script>
    // Disable back and forward navigation
    (function() {
      // Listen to the popstate event to prevent the user from using the back or forward buttons
      window.history.pushState(null, null, window.location.href);
      window.onpopstate = function() {
        window.history.pushState(null, null, window.location.href);
      };

      // Optionally, prevent right-clicking on the page to access browser navigation options
      document.addEventListener('contextmenu', function(e) {
        e.preventDefault();  // Disable right-click context menu
      });
    })();
  </script>


  <style>
    /* Same styles as before — no changes here */
    * {
      box-sizing: border-box;
      scroll-behavior: smooth;
      font-family: 'Poppins', sans-serif;
    }

    body {
      margin: 0;
      background: linear-gradient(to right, #f7f8fa, #e0f7fa);
      background: url('back4.jpg') no-repeat center center fixed;
      background-size: cover;
      color: #333;
    }

    header {
      background: linear-gradient(90deg,#3d52c5, rgb(15, 154, 152));
      color: #fff;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 30px;
    }

    .title {
      font-size: 1.8rem;
      font-weight: 600;
      text-shadow: 1px 1px 2px #00000033;
    }

    nav a {
      color: white;
      text-decoration: none;
      margin-left: 18px;
      font-weight: 500;
      transition: 0.3s;
    }

    nav a:hover {
      text-decoration: underline;
    }

    .container {
      text-align: center;
      padding: 50px 20px;
    }

    .subtitle {
      font-size: 1.1rem;
      margin-top: 10px;
      color: #666;
    }
    .container {
    padding: 20px;
  }
  .card {
    margin-bottom: 20px;
  }


    .selection-container {
      margin-top: 40px;
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 40px;
    }

    .card {
      width: 240px;
      height: 160px;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(8px);
      border-radius: 20px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 1.4rem;
      font-weight: 600;
      color: #003366;
      cursor: pointer;
      transition: all 0.3s ease;
      opacity: 1;
    }

    .card:hover {
      transform: scale(1.05);
    }

    .card.disabled {
      opacity: 0.5;
      pointer-events: none;
    }

    .card.selected {
      background: #00b4db;
      color: white;
    }

    .form-section {
      margin-top: 30px;
      display: none;
      animation: fadeInForm 0.8s ease;
    }

    .form-section input {
      padding: 10px;
      margin: 10px;
      width: 250px;
      border: 1.5px solid #ccc;
      border-radius: 10px;
      font-size: 1rem;
    }

    .btn {
      padding: 12px 25px;
      border: none;
      border-radius: 25px;
      background: linear-gradient(to right, #00c6ff, #0072ff);
      color: white;
      font-size: 1rem;
      margin: 15px 10px;
      cursor: pointer;
      transition: 0.3s ease;
    }

    .btn:hover {
      background: linear-gradient(to right, #00c6ff, #66ffcc);
      color: #000;
    }

    .recommendation {
      margin-top: 30px;
      background-color: #ffffffcc;
      padding: 25px;
      border-radius: 12px;
      width: fit-content;
      margin-inline: auto;
      box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }

    footer {
      color: black;
      padding: 20px;
      text-align: center;
      margin-top: 70px;
      font-size: 0.8rem;
    }

    .social-icons img {
      width: 20px;
      margin: 0 5px;
      transition: 0.3s ease;
    }

    .social-icons img:hover {
      transform: scale(1.2);
    }

    @media (max-width: 768px) {
      .card {
        width: 90%;
        margin: 10px;
      }

      header {
        flex-direction: column;
        align-items: flex-start;
      }

      nav {
        margin-top: 10px;
      }
    }
  </style>
</head>

<body>

<header>
  <h1 class="title">Welcome, <?php echo htmlspecialchars($name); ?>!</h1>
  <nav>
    <a href="main1.php"><i class="fas fa-house"></i> Home</a>
    <a href="history.html"><i class="fas fa-clock-rotate-left"></i> History</a>
    <a href="feedback.html"><i class="fas fa-comments"></i> Feedback</a>
    <a href="logout.php" onclick="return confirm('Are you sure you want to logout?')"><i class="fas fa-right-from-bracket"></i> Logout</a>
  </nav>
</header>

<div class="container">
  <div class="title" style="color:#003366;">Choose Your Treatment Method</div>
  <div class="subtitle">Get personalized recommendations</div>

  <div class="selection-container">
    <div class="card" id="allopathyCard" onclick="selectType('Allopathy')">Allopathy</div>
    <div class="card" id="ayurvedicCard" onclick="selectType('Ayurvedic')">Ayurvedic</div>
  </div>

  <!-- Hidden input to store the selected type -->
  <input type="hidden" id="recommendationType" value="">

  <div class="form-section" id="formSection">
    <h3 id="formTitle">Enter Your Details</h3>
    <input type="number" id="age" placeholder="Enter your age" />
    <input type="text" id="symptoms" placeholder="Enter symptoms" list="symptomsList" />
    <datalist id="symptomsList"></datalist>
    <br>
    <button class="btn" onclick="fetchRecommendations()">Recommend</button>
    <button class="btn" onclick="clearRecommendation()">Clear</button>
  </div>

  <div class="recommendation" id="recommendationResults">
    <h3>Recommendations</h3>
    <!-- <p class="diagnosis">Diagnosis will appear here</p> -->
  </div>
</div>

<footer>
  <p>Contact us on:</p>
  <div class="social-icons">
    <a href="https://www.instagram.com/online_doctor43?utm_source=qr&igsh=Y2Y4ZjQ3dHhwbWtw" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" alt="Instagram"></a>
    <a href="https://twitter.com" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" alt="Twitter"></a>
    <a href="https://www.facebook.com/share/16SJkSEe8y/" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Facebook"></a>
    <a href="https://chat.whatsapp.com/HqZdd2NdGZdFAQZapWpcMM" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/733/733585.png" alt="WhatsApp"></a>
  </div>
  <p>&copy; 2025 Health Recommendation System</p>
</footer>

<script>
function selectType(type) {
  document.getElementById("recommendationType").value = type;
  document.getElementById("formSection").style.display = "block";

  const allopathyCard = document.getElementById("allopathyCard");
  const ayurvedicCard = document.getElementById("ayurvedicCard");

  if (type === "Allopathy") {
    allopathyCard.classList.add("selected");
    ayurvedicCard.classList.add("disabled");
  } else {
    ayurvedicCard.classList.add("selected");
    allopathyCard.classList.add("disabled");
  }

  updateInputSuggestions(type);
}

function updateInputSuggestions(type) {
  const datalist = document.getElementById("symptomsList");
  datalist.innerHTML = "";

  const endpoint = type === "Ayurvedic"
    ? "http://127.0.0.1:8000/ayurvedic_disease_keywords/"
    : "http://127.0.0.1:8000/symptom_keywords/";

  fetch(endpoint)
    .then((res) => res.json())
    .then((data) => {
      data.forEach((item) => {
        const option = document.createElement("option");
        option.value = item;
        datalist.appendChild(option);
      });
    })
    .catch((err) => console.error("Error loading suggestions:", err));
}

async function fetchRecommendations() {
  const type = document.getElementById("recommendationType").value;
  const age = parseInt(document.getElementById("age").value);
  const symptoms = document.getElementById("symptoms").value.trim();
  const resultsDiv = document.getElementById("recommendationResults");

  resultsDiv.innerHTML = "";

  if (!symptoms) {
    alert("Please enter symptoms.");
    return;
  }
  if (isNaN(age) || age < 18 || age > 50) {
    alert("Age must be between 18 and 50.");
    return;
  }

  const apiUrl = type === "Allopathy"
    ? "http://127.0.0.1:8000/recommend_medicine/"
    : "http://127.0.0.1:8000/recommend_remedy/";

  try {
    const response = await fetch(apiUrl, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ input_symptoms: symptoms }),
    });

    const data = await response.json();
    let outputHTML = `<h3>Recommendations</h3>`;
    let recommendation = "";
    let disease = "";

    if (type === "Allopathy" && data.recommended_medicines.length > 0) {
      data.recommended_medicines.forEach((med) => {
        outputHTML += `
          <div style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">
            <p><strong>Disease:</strong> ${med.disease}</p>
            <p><strong>Medicine:</strong> ${med.recommended_medicine}</p>
            <p><strong>Rating:</strong> ${med.rating}</p>
          </div>`;
      });

      recommendation = data.recommended_medicines[0].recommended_medicine;
      disease = data.recommended_medicines[0].disease;

    } else if (type === "Ayurvedic" && data.recommended_remedies.length > 0) {
      data.recommended_remedies.forEach((rem) => {
        outputHTML += `
          <div style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">
            <p><strong>Disease:</strong> ${rem.disease}</p>
            <p><strong>Remedy:</strong> ${rem.remedy}</p>
          </div>`;
      });

      recommendation = data.recommended_remedies[0].remedy;
      disease = data.recommended_remedies[0].disease;
    } else {
      outputHTML += `<p>No recommendations available.</p>`;
    }

    resultsDiv.innerHTML = outputHTML;

    await fetch("save_data.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        recommendationType: type,
        age,
        symptoms,
        disease,
        recommendation,
      }),
    });
  } catch (error) {
    console.error("Error:", error);
    resultsDiv.innerHTML = `<p class="error">Failed to fetch recommendations.</p>`;
  }
}

function clearRecommendation() {
  document.getElementById("recommendationResults").innerHTML = `<h3>Recommendations</h3><p class="diagnosis">Diagnosis will appear here</p>`;
  document.getElementById("age").value = "";
  document.getElementById("symptoms").value = "";
  document.getElementById("recommendationType").value = "";

  // Reset card styles
  const allopathyCard = document.getElementById("allopathyCard");
  const ayurvedicCard = document.getElementById("ayurvedicCard");

  allopathyCard.classList.remove("selected", "disabled");
  ayurvedicCard.classList.remove("selected", "disabled");

  // Hide the form section again
  document.getElementById("formSection").style.display = "none";
}

</script>

</body>
</html>

