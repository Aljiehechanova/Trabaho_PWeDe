<!DOCTYPE html>
<html lang="en">
<script>
    function redirectToPage() {
      let jobSeeker = document.getElementById("job-seeker").checked;
      let client = document.getElementById("client").checked;

      if (jobSeeker) {
        window.location.href = "userR.php";
      } else if (client) {
        window.location.href = "clientR.php";
      } else {
        alert("Please select an option before proceeding.");
      }
    }
  </script>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trabaho PWeDe - Choice</title>
  <link rel="stylesheet" href="../assets/css/choice.css">
</head>
<body>
  <div class="choice-container">
    <h1>Join as Job Seeker or Client</h1>
    <div class="options">
      <div class="option">
        <input type="radio" id="job-seeker" name="user-type">
        <label for="job-seeker">
          <div class="icon"></div>
          <p>I'm a job seeker, looking for work</p>
        </label>
      </div>
      <div class="option">
        <input type="radio" id="client" name="user-type">
        <label for="client">
          <div class="icon"></div>
          <p>I'm a client, appoint for opportunity</p>
        </label>
      </div>
    </div>
    <button class="btn-primary" onclick="redirectToPage()">Create Account</button>
    <p>Already have an account? <a href="login.php">Log In</a></p>
  </div>
</body>
</html>
