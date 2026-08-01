const API_BASE = "http://127.0.0.1:8000/api/v1";

async function updateCartCount() {

    const token = localStorage.getItem("token");

    if (!token) return;

    try {

        const response = await fetch(`${API_BASE}/cart`, {
            headers: {
                "Accept": "application/json",
                "Authorization": `Bearer ${token}`
            }
        });

        const data = await response.json();

        if (data.success) {

            const badge = document.getElementById("cart-count");

            if (badge) {
                badge.textContent = data.data.items_count || 0;
            }
        }

    } catch (error) {

        console.error("Cart Count Error:", error);

    }
}

window.addEventListener("DOMContentLoaded", updateCartCount);