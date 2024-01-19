<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feedback Example</title>
  <style>
    button {
      padding: 10px 20px;
      font-size: 16px;
      cursor: pointer;
    }

    /* Add a style for the visual feedback */
    .vibrate {
      animation: vibrateAnimation 200ms ease-out;
    }

    @keyframes vibrateAnimation {
      0% { transform: translateX(0); }
      25% { transform: translateX(-5px); }
      50% { transform: translateX(5px); }
      75% { transform: translateX(-5px); }
      100% { transform: translateX(0); }
    }
  </style>
</head>
<body>

<button onclick="provideFeedback()">Click me for feedback</button>

<script>
function provideFeedback() {
  // Check if the Vibration API is supported by the browser
  const button = document.querySelector('button');
    button.classList.add('vibrate');

    // Add an event listener to remove the visual feedback after the animation duration
    button.addEventListener('animationend', () => {
      button.classList.remove('vibrate');
    });
  
}
</script>

</body>
</html>
