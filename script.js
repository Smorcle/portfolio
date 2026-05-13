// =============================================
//  ARN.DEV — Indie Platformer Portfolio
//  script.js
// =============================================

document.addEventListener("DOMContentLoaded", () => {

    // ---- 1. CUSTOM CURSOR ----
    const cursor = document.getElementById('cursor');
    document.addEventListener('mousemove', e => {
        cursor.style.left = e.clientX + 'px';
        cursor.style.top  = e.clientY + 'px';
    });
    document.addEventListener('mousedown', () => {
        cursor.style.transform = 'translate(-50%,-50%) scale(0.7)';
    });
    document.addEventListener('mouseup', () => {
        cursor.style.transform = 'translate(-50%,-50%) scale(1)';
    });

    // ---- 2. STAR GENERATION ----
    const starsContainer = document.getElementById('stars');
    for (let i = 0; i < 80; i++) {
        const s = document.createElement('div');
        s.className = 'star';
        s.style.left = Math.random() * 100 + '%';
        s.style.top  = Math.random() * 80 + '%';
        s.style.setProperty('--d',     (2 + Math.random() * 4) + 's');
        s.style.setProperty('--delay', (Math.random() * 4) + 's');
        if (Math.random() > 0.8) {
            const colors = ['#ff6b9d', '#c084fc', '#38bdf8', '#fbbf24'];
            s.style.background = colors[Math.floor(Math.random() * colors.length)];
            s.style.width  = '3px';
            s.style.height = '3px';
        }
        starsContainer.appendChild(s);
    }

    // ---- 3. DARK / LIGHT MODE ----
    const toggleBtn = document.getElementById("dark-mode-toggle");
    toggleBtn.addEventListener("click", () => {
        document.body.classList.toggle("light-mode");
        const isLight = document.body.classList.contains("light-mode");
        toggleBtn.textContent = isLight ? "☾ DARK" : "☀ LIGHT";
    });
    // ---- 6. SCROLL REVEAL & STAT BAR ANIMATION (MOVED UP!) ----
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                // Fill the stat bars with animation
                entry.target.querySelectorAll('.stat-fill[data-val]').forEach(bar => {
                    bar.style.width = bar.dataset.val + '%';
                });
            }
        });
    }, { threshold: 0.15 });

   
    const container = document.getElementById("project-container");
// FETCH PROJECTS FROM THE DATABASE
fetch("get_projects.php")
    .then(response => response.json())
    .then(data => {
        // If there are projects in the database, render them
        if (data.length > 0) {
            renderProjects(data);
        } else {
            // If the database is completely empty, show the user a message
            document.getElementById('project-container').innerHTML = 
                "<p style='color:var(--muted); text-align:center; font-family:var(--mono);'>There are no projects added yet.</p>";
        }
    })
    .catch(error => {
        console.error("Database error:", error);
        document.getElementById('project-container').innerHTML = 
            "<p style='color:#ff6b9d; text-align:center;'>Database connection error!</p>";
    });

    

    function escapeHtml(value) {
        const str = String(value ?? '');
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function renderProjects(data) {
        if (!data.length) {
            container.innerHTML = "<p style='color:var(--muted);font-family:var(--mono)'>There are no projects added yet.</p>";
            return;
        }
        data.forEach((p, i) => {
            const projectTitle = escapeHtml(p.title);
            const projectDescription = escapeHtml(p.description || '');
            const projectLink = String(p.project_link || p.link || '').trim();
            const safeLink = /^https?:\/\//i.test(projectLink) ? projectLink : '';
            const projectTag = escapeHtml(p.tag || 'PROJE');
            const techList = Array.isArray(p.tech)
                ? p.tech
                : (typeof p.tech === 'string' ? p.tech.split(',').map(t => t.trim()).filter(Boolean) : []);

            const card = document.createElement("div");
            card.className = "project-card reveal";
            card.style.transitionDelay = (i * 0.1) + 's';
            card.innerHTML = `
                <div class="project-card-tag">${projectTag}</div>
                <h3>${projectTitle}</h3>
                <p>${projectDescription}</p>
                <div class="project-card-footer">
                    <div class="project-tech">
                        ${techList.map(t => `<span class="tech-badge">${escapeHtml(t)}</span>`).join('')}
                    </div>
                    ${safeLink ? `<a href="${safeLink}" target="_blank" rel="noopener noreferrer" class="project-link">VIEW →</a>` : ''}
                </div>
            `;
            container.appendChild(card);
            // Register the newly added card with the observer
            observer.observe(card);
        });
    }

    // ---- 5. FORM VALIDATION & SUBMISSION ----
    const contactForm  = document.getElementById("contactForm");
    const formResponse = document.getElementById("form-response");
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

    contactForm.addEventListener("submit", (e) => {
        e.preventDefault();

        const name    = document.getElementById("name").value.trim();
        const email   = document.getElementById("email").value.trim();
        const message = document.getElementById("message").value.trim();

        // Client-side validation
        if (name.length < 3) {
            formResponse.innerHTML = "<span style='color:#ff6b9d'>! ERROR: Name must be at least 3 characters long.</span>";
            return;
        }
        if (!emailRegex.test(email)) {
            formResponse.innerHTML = "<span style='color:#ff6b9d'>! ERROR: Please enter a valid email address.</span>";
            return;
        }
        if (message.length < 10) {
            formResponse.innerHTML = "<span style='color:#ff6b9d'>! ERROR: Message is too short.</span>";
            return;
        }

        formResponse.innerHTML = "<span style='color:#c084fc'>▶ Sending...</span>";

        // Create FormData and send it to the PHP file
        const formData = new FormData();
        formData.append("name", name);
        formData.append("email", email);
        formData.append("message", message);

        fetch("save_message.php", { 
            method: "POST", 
            body: formData 
        })
        .then(response => response.text())
        .then(data => {
            // Display the success/failure message returned from PHP
            formResponse.innerHTML = `<span style='color:#38bdf8'>✓ ${data}</span>`;
            contactForm.reset(); // Clear the form
        })
        .catch(error => {
            console.error(" Error:", error);
            formResponse.innerHTML = "<span style='color:#ff6b9d'>! Connection error. Message could not be sent.</span>";
        });
    });

    

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    // ---- 7. EXP BAR (on page load) ----
    setTimeout(() => {
        const expFill = document.getElementById('exp-fill');
        if (expFill) expFill.style.width = '74%';
    }, 800);

});
// ---- 8. BACK-TO-TOP BUTTON VISIBILITY ----
    const backToTopBtn = document.querySelector('.back-to-top');

    window.addEventListener('scroll', () => {
        // Add the 'show' class to the button when the page is scrolled down 800 pixels
        if (window.scrollY > 800) {
            backToTopBtn.classList.add('show');
        } else {
            // Hide the button again when scrolling back up
            backToTopBtn.classList.remove('show');
        }
    });