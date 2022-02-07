const refresh = document.querySelector("#refresh");
let recentMatches = document.querySelector("#recentMatches");

refresh.addEventListener("click", async (e) => {
  e.preventDefault();

  const response = await fetch("recentMatches.php");
  const result = await response.text();
  recentMatches.innerHTML += result;
});
