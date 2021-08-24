const form = document.getElementById("form");
const year = document.getElementById("year");
const closeIcon = document.getElementById("closeIcon");
const yearInput = document.getElementById("year");
const titleInput = document.getElementById("title");
const imdbidInput = document.getElementById("imdbid");
const typeSelector = document.getElementById("type");

year.addEventListener("keydown", (e) => {
	if (!e.key.match(/^[\d\s-]+$/) && e.key != "Backspace") e.preventDefault();
});

imdbidInput.addEventListener("keydown", (e) => {
	if (!e.key.match(/^[\d]+$/) && e.key != "Backspace") e.preventDefault();
});

if (closeIcon) {
	closeIcon.addEventListener("click", ({ target }) => {
		target.parentElement.remove();
	});
}

form.addEventListener("submit", (e) => {
	e.preventDefault();

	const types = ["Movie", "Serie", "Episode"];

	document.querySelectorAll(".alert").forEach((alert) => alert.remove());
	document.querySelectorAll(".error").forEach((error) => error.remove());

	if (!titleInput.value.trim()) createError(titleInput);
	if (!yearInput.value.trim()) createError(yearInput);

	if (!yearInput.value.match(/^[\d\s-]+$/))
		createError(yearInput, "Only numbers");
	if (imdbidInput.value && !imdbidInput.value.match(/^[\d]+$/))
		createError(imdbidInput, "Only numbers");
	if (!types.includes(typeSelector.value))
		createError(typeSelector, "Option not allowed");

	if (!document.querySelector(".error")) {
		form.submit();
	}
});

function createError(input, message = "Field required") {
	const span = document.createElement("span");
	span.classList.add("error");
	span.textContent = message;

	input.parentElement.appendChild(span);
}
