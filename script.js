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

    // ---- 2. YILDIZ OLUŞTURMA ----
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

    // ---- 3. GECE / GÜNDÜZ MODU ----
    const toggleBtn = document.getElementById("dark-mode-toggle");
    toggleBtn.addEventListener("click", () => {
        document.body.classList.toggle("light-mode");
        const isLight = document.body.classList.contains("light-mode");
        toggleBtn.textContent = isLight ? "☾ KARANLIK" : "☀ IŞIK";
    });
    // ---- 6. SCROLL REVEAL & STAT BAR ANİMASYON (YUKARI TAŞINDI!) ----
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                // Stat barları animasyonla doldur
                entry.target.querySelectorAll('.stat-fill[data-val]').forEach(bar => {
                    bar.style.width = bar.dataset.val + '%';
                });
            }
        });
    }, { threshold: 0.15 });

    // ---- 4. PROJELER (Statik Veri — Backend hazır olunca AJAX ile değiştir) ----
    const projects = [
        {
            tag: "SİSTEM YAZILIMI",
            title: "ChronicleLogic",
            description: "Özelleştirilebilir sorgu dili ile gerçek zamanlı log analizi yapan, yüksek verimli bir log yönetim motoru. Saniyede 100K+ satır işleme kapasitesine sahip.",
            tech: ["C++", "PostgreSQL", "ZeroMQ"],
            link: "#"
        },
        {
            tag: "GÖMÜLÜ SİSTEM",
            title: "BeepGuide",
            description: "Görme engelli bireyler için geliştirilmiş ultrasonik sensör tabanlı navigasyon sistemi. Raspberry Pi üzerinde çalışan, gerçek zamanlı ses geri bildirim algoritmasıyla donatılmış.",
            tech: ["Python", "C", "Raspberry Pi", "I2C"],
            link: "#"
        },
        {
            tag: "WEB PLATFORMU",
            title: "DevPortfolio CMS",
            description: "Geliştiriciler için tasarlanmış, sürükle-bırak proje yönetimi, analitik dashboard ve otomatik SEO optimizasyonu sunan açık kaynaklı portföy yönetim sistemi.",
            tech: ["React", "Node.js", "MongoDB"],
            link: "#"
        },
        {
            tag: "VERİTABANI",
            title: "QueryForge",
            description: "Karmaşık SQL sorgularını görsel olarak oluşturup optimize eden, performans analizi ve otomatik indeks önerisi sunan veritabanı yönetim aracı.",
            tech: ["TypeScript", "MySQL", "Redis"],
            link: "#"
        }
    ];

    const container = document.getElementById("project-container");
// Backend'den (veritabanından) projeleri çeken AJAX isteği
    fetch("get_projects.php")
        .then(response => response.json())
        .then(data => {
            // Veritabanından gelen data varsa onu kullan, 
            // veritabanı boşsa geçici olarak JS içindeki statik projeleri (projects) göster
            renderProjects(data.length > 0 ? data : projects);
        })
        .catch(error => {
            console.error("Veritabanına bağlanılamadı:", error);
            // Sunucuda hata olursa site boş kalmasın diye statik projeleri yükle
            renderProjects(projects); 
        });

    

    function renderProjects(data) {
        if (!data.length) {
            container.innerHTML = "<p style='color:var(--muted);font-family:var(--mono)'>Henüz proje eklenmemiş.</p>";
            return;
        }
        data.forEach((p, i) => {
            const card = document.createElement("div");
            card.className = "project-card reveal";
            card.style.transitionDelay = (i * 0.1) + 's';
            card.innerHTML = `
                <div class="project-card-tag">${p.tag || 'PROJE'}</div>
                <h3>${p.title}</h3>
                <p>${p.description}</p>
                <div class="project-card-footer">
                    <div class="project-tech">
                        ${(p.tech || []).map(t => `<span class="tech-badge">${t}</span>`).join('')}
                    </div>
                    ${p.link ? `<a href="${p.link}" target="_blank" class="project-link">GÖRÜNTÜLE →</a>` : ''}
                </div>
            `;
            container.appendChild(card);
            // Yeni eklenen kartı observer'a kaydet
            observer.observe(card);
        });
    }

    // ---- 5. FORM VALİDASYON & GÖNDERİM ----
    const contactForm  = document.getElementById("contactForm");
    const formResponse = document.getElementById("form-response");

    contactForm.addEventListener("submit", (e) => {
        e.preventDefault();

        const name    = document.getElementById("name").value.trim();
        const email   = document.getElementById("email").value.trim();
        const message = document.getElementById("message").value.trim();

        // Client-side validasyon
        if (name.length < 3) {
            formResponse.innerHTML = "<span style='color:#ff6b9d'>! HATA: Ad en az 3 karakter olmalı.</span>";
            return;
        }
        if (!email.includes("@") || !email.includes(".")) {
            formResponse.innerHTML = "<span style='color:#ff6b9d'>! HATA: Geçerli e-posta girin.</span>";
            return;
        }
        if (message.length < 10) {
            formResponse.innerHTML = "<span style='color:#ff6b9d'>! HATA: Mesaj çok kısa.</span>";
            return;
        }

        formResponse.innerHTML = "<span style='color:#c084fc'>▶ GÖNDERİLİYOR...</span>";

        // FormData oluşturup PHP dosyasına gönderiyoruz
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
            // PHP'den dönen başarılı/başarısız mesajını ekrana yazdır
            formResponse.innerHTML = `<span style='color:#38bdf8'>✓ ${data}</span>`;
            contactForm.reset(); // Formu temizle
        })
        .catch(error => {
            console.error("Hata:", error);
            formResponse.innerHTML = "<span style='color:#ff6b9d'>! Bağlantı hatası. Mesaj iletilemedi.</span>";
        });
    });

    

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    // ---- 7. EXP BAR (sayfa yüklenince) ----
    setTimeout(() => {
        const expFill = document.getElementById('exp-fill');
        if (expFill) expFill.style.width = '74%';
    }, 800);

});