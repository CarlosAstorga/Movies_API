const form = document.getElementById("form");
const year = document.getElementById("year");
const closeIcon = document.getElementById("closeIcon");
const yearInput = document.getElementById("year");
const fileInput = document.getElementById("fileInput");
const titleInput = document.getElementById("title");
const imdbidInput = document.getElementById("imdbid");
const typeSelector = document.getElementById("type");
const fileInputText = document.querySelector(".file-name");

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

fileInput.addEventListener("change", () => {
	const lastChild = fileInput.parentElement.lastElementChild;
	if (lastChild.className == "error") lastChild.remove();

	const { name, size } = fileInput.files[0];
	const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;

	let errorMsg = "";
	if (!allowedExtensions.exec(name)) errorMsg = "File extension not allowed";
	else if ((size / 1024 / 1024).toFixed(2) > 2) errorMsg = "File too large";
	else fileInputText.textContent = name;

	if (errorMsg) {
		fileInput.value = "";
		fileInputText.textContent = "Upload poster";
		createError(fileInput, errorMsg);
	}
});

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
