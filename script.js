let bookList = [];

window.onload = function () {
  fetch("get_books.php")
    .then(res => res.json())
    .then(data => {
      bookList = data;
      const booksDiv = document.getElementById("books");
      data.forEach(book => {
        const div = document.createElement("div");
        div.className = "book-card";
        div.innerHTML = `
          <img src="${book.image_url}" alt="${book.title}" class="book-image"><br>
          <strong>${book.title}</strong><br>
          by ${book.author}<br>
          Price: $${book.price}<br>
          In Stock: ${book.stock}<br>
          <button class="add-cart-btn" onclick='addToCart(${book.id})'>🛒 Add to Cart</button>`;
        booksDiv.appendChild(div);
      });
    });
};

function addToCart(bookId) {
  fetch("cart_add.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: "book_id=" + bookId
  })
    .then(res => res.text())
    .then(msg => {
      alert(msg);
    });
}

function sendChat() {
  const inputField = document.getElementById("chatInput");
  const input = inputField.value.trim();
  if (input === "") return;
  inputField.value = "";

  appendMessage(input, "user");

  const lowered = input.toLowerCase();
  let reply = "";

  const faqMap = [
    { keywords: ["order"], answer: "To order a book, go to the cart page and proceed to checkout." },
    { keywords: ["payment", "pay", "methods"], answer: "We accept M-Pesa, Visa, Mastercard, and PayPal." },
    { keywords: ["delivery", "deliver", "time"], answer: "Delivery typically takes 2-3 business days." },
    { keywords: ["return", "policy"], answer: "Returns are accepted within 7 days if books are in original condition." },
    { keywords: ["available", "books"], answer: null }
  ];

  for (const item of faqMap) {
    if (item.keywords.some(k => lowered.includes(k))) {
      reply = item.answer ?? listAllBooks();
      appendMessage(reply, "bot");
      return;
    }
  }

  const matches = bookList.filter(book =>
    lowered.includes(book.title.toLowerCase()) ||
    lowered.includes(book.author.toLowerCase())
  );

  if (matches.length > 0) {
    reply = matches.map(book =>
      `${book.title} by ${book.author} - $${book.price} (${book.stock} in stock)`
    ).join("\n");
  } else {
    reply = "Sorry, I couldn't find that book or answer. Try asking about available books, payment, or delivery.";
  }

  appendMessage(reply, "bot");
}

function appendMessage(message, sender) {
  const chatHistory = document.getElementById("chat-history");
  const msgDiv = document.createElement("div");
  msgDiv.className = `chat-bubble ${sender}`;
  msgDiv.innerText = message;
  chatHistory.appendChild(msgDiv);
  chatHistory.scrollTop = chatHistory.scrollHeight;
}

function listAllBooks() {
  if (bookList.length === 0) return "No books found in the store.";
  return bookList.map(book =>
    `${book.title} by ${book.author} - $${book.price} (in stock)`
  ).join("\n");
}

document.getElementById("chat-icon").addEventListener("click", () => {
  const popup = document.getElementById("chat-popup");
  popup.classList.toggle("hidden");
  popup.style.display = popup.classList.contains("hidden") ? "none" : "flex";
});
