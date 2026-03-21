const body = document.body;
const themeToggle = document.getElementById("theme-toggle");
const themeLabel = themeToggle?.querySelector(".theme-toggle-label");
const alertBox = document.getElementById("form-alert");

const THEME_KEY = "flowtrack-theme";

function applyTheme(theme) {
  body.dataset.theme = theme;
  if (themeLabel) {
    themeLabel.textContent = theme === "dark" ? "Tmavý režim" : "Svetlý režim";
  }
  localStorage.setItem(THEME_KEY, theme);
}

function showAlert(message, type) {
  if (!alertBox) {
    return;
  }

  alertBox.hidden = false;
  alertBox.textContent = message;
  alertBox.className = `alert is-${type}`;
}

function hideAlert() {
  if (!alertBox) {
    return;
  }

  alertBox.hidden = true;
  alertBox.textContent = "";
  alertBox.className = "alert";
}

function clearErrors(form) {
  form.querySelectorAll(".field").forEach((field) => {
    field.classList.remove("is-invalid");
    const error = field.querySelector(".field-error");
    if (error) {
      error.textContent = "";
    }
  });
}

function setFieldError(input, message) {
  const field = input.closest(".field");
  if (!field) {
    return;
  }

  field.classList.add("is-invalid");
  const error = field.querySelector(".field-error");
  if (error) {
    error.textContent = message;
  }
}

function validateLogin(form) {
  const email = form.elements.email;
  const password = form.elements.password;
  let valid = true;

  clearErrors(form);

  if (!email.value.trim()) {
    setFieldError(email, "Email je povinný.");
    valid = false;
  } else if (!/^\S+@\S+\.\S+$/.test(email.value.trim())) {
    setFieldError(email, "Zadaj platný email.");
    valid = false;
  }

  if (!password.value.trim()) {
    setFieldError(password, "Heslo je povinné.");
    valid = false;
  } else if (password.value.trim().length < 6) {
    setFieldError(password, "Heslo musí mať aspoň 6 znakov.");
    valid = false;
  }

  return valid;
}

function validateRegister(form) {
  const name = form.elements.name;
  const workspace = form.elements.workspace;
  const email = form.elements.email;
  const password = form.elements.password;
  const confirmPassword = form.elements.confirmPassword;
  const terms = form.elements.terms;
  let valid = true;

  clearErrors(form);

  if (!name.value.trim() || name.value.trim().length < 3) {
    setFieldError(name, "Zadaj celé meno aspoň s 3 znakmi.");
    valid = false;
  }

  if (!workspace.value.trim() || workspace.value.trim().length < 3) {
    setFieldError(workspace, "Workspace musí mať aspoň 3 znaky.");
    valid = false;
  }

  if (!email.value.trim()) {
    setFieldError(email, "Email je povinný.");
    valid = false;
  } else if (!/^\S+@\S+\.\S+$/.test(email.value.trim())) {
    setFieldError(email, "Zadaj platný email.");
    valid = false;
  }

  if (!password.value.trim()) {
    setFieldError(password, "Heslo je povinné.");
    valid = false;
  } else if (password.value.length < 8) {
    setFieldError(password, "Heslo musí mať aspoň 8 znakov.");
    valid = false;
  }

  if (!confirmPassword.value.trim()) {
    setFieldError(confirmPassword, "Potvrď heslo.");
    valid = false;
  } else if (password.value !== confirmPassword.value) {
    setFieldError(confirmPassword, "Heslá sa nezhodujú.");
    valid = false;
  }

  if (!terms.checked) {
    showAlert("Pre registráciu je potrebné potvrdiť súhlas.", "error");
    valid = false;
  }

  return valid;
}

function attachPasswordToggles() {
  document.querySelectorAll(".password-toggle").forEach((button) => {
    button.addEventListener("click", () => {
      const input = button.parentElement?.querySelector("input");
      if (!input) {
        return;
      }

      const reveal = input.type === "password";
      input.type = reveal ? "text" : "password";
      button.textContent = reveal ? "Skryť" : "Zobraziť";
    });
  });
}

function attachFormHandlers() {
  const loginForm = document.getElementById("login-form");
  const registerForm = document.getElementById("register-form");

  loginForm?.addEventListener("submit", (event) => {
    event.preventDefault();
    hideAlert();

    if (!validateLogin(loginForm)) {
      showAlert("Skontroluj prihlasovacie údaje.", "error");
      return;
    }

    showAlert(
      "Údaje vyzerajú v poriadku. Po napojení backendu sa tu spustí reálne prihlásenie.",
      "success"
    );
  });

  registerForm?.addEventListener("submit", (event) => {
    event.preventDefault();
    hideAlert();

    if (!validateRegister(registerForm)) {
      if (registerForm.querySelector(".field.is-invalid")) {
        showAlert("Registrácia potrebuje doplniť chýbajúce alebo nesprávne údaje.", "error");
      }
      return;
    }

    showAlert(
      "Formulár je vyplnený správne. Po napojení backendu sa účet uloží do databázy.",
      "success"
    );
  });
}

function init() {
  const storedTheme = localStorage.getItem(THEME_KEY) || "dark";

  applyTheme(storedTheme);
  attachPasswordToggles();
  attachFormHandlers();

  themeToggle?.addEventListener("click", () => {
    const nextTheme = body.dataset.theme === "dark" ? "light" : "dark";
    applyTheme(nextTheme);
  });
}

init();
