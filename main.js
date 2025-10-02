// Función de implementación de alerta (reemplaza alert() nativo)
function alert(message) {
    const alertModal = document.createElement('div');
    alertModal.style.cssText = `
        position: fixed; top: 20px; right: 20px; background: #fff;
        padding: 15px 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1000; border-left: 5px solid #77216F; font-family: 'Ubuntu', sans-serif;
        font-size: 0.9rem; color: #333; transition: opacity 0.3s ease-out;
    `;
    alertModal.textContent = message;
    document.body.appendChild(alertModal);

    setTimeout(() => {
        alertModal.style.opacity = '0';
        setTimeout(() => alertModal.remove(), 300);
    }, 3000);
}

// ----------------------------------------------------------------------
// --- CÓDIGO DE COPIA Y NAVEGACIÓN ---
// ----------------------------------------------------------------------

// Función que maneja el click en el botón "Copiar"
async function handleCopyClick(event) {
    event.stopPropagation();
    
    const button = event.target;
    const block = button.parentElement;
    
    // Obtiene el texto del bloque (sin el texto del botón)
    const text = block.textContent.replace(button.textContent, '').trim();

    try {
        await navigator.clipboard.writeText(text);
        button.textContent = '¡Copiado!';
        setTimeout(() => {
            button.textContent = 'Copiar';
        }, 2000);
    } catch (err) {
        console.error('Error al copiar con Clipboard API, usando fallback:', err);
        try {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);

            button.textContent = '¡Copiado!';
            setTimeout(() => {
                button.textContent = 'Copiar';
            }, 2000);

        } catch (fallbackErr) {
            console.error('Fallback failed:', fallbackErr);
            button.textContent = 'Error';
            setTimeout(() => {
                button.textContent = 'Copiar';
            }, 2000);
        }
    }
}

// Función que maneja el click en el div entero
function handleSnippetClick(event) {
    if (!event.target.classList.contains('copy-btn')) {
        const button = event.currentTarget.querySelector('.copy-btn');
        if (button) {
            button.click();
        }
    }
}

// Configura los listeners de copia en los snippets
function setupCopyListeners() {
    document.querySelectorAll('.copy-btn').forEach(button => {
        button.removeEventListener('click', handleCopyClick); 
        button.addEventListener('click', handleCopyClick);
    });

    document.querySelectorAll('.code-snippet').forEach(snippetDiv => {
        snippetDiv.removeEventListener('click', handleSnippetClick); 
        snippetDiv.addEventListener('click', handleSnippetClick);
    });
    
    console.log('Copy listeners configurados. Botones encontrados:', document.querySelectorAll('.copy-btn').length);
}

// Configura la navegación dinámica y el menú hamburguesa
function setupNavigationAndHamburger() {
    const wrapper = document.getElementById('wrapper');
    const navLinks = document.querySelectorAll('.nav-link');
    
    if (!wrapper) {
        console.error('ERROR: No se encontró el elemento #wrapper');
        return;
    }
    
    console.log('Navegación iniciada. Enlaces encontrados:', navLinks.length);
    
    // 1. LÓGICA DE HAMBURGUESA
    const hamburger = document.getElementById('hamburger');
    const menuItemsContainer = document.querySelector('.nav-links');
    
    if (hamburger && menuItemsContainer) {
        hamburger.addEventListener('click', function() {
            menuItemsContainer.classList.toggle('active');
        });
        
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (menuItemsContainer.classList.contains('active')) {
                    menuItemsContainer.classList.remove('active');
                }
            });
        });
    }

    // Función para establecer el enlace activo
    function setActiveLink(categoryKey) {
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('data-category') === categoryKey) {
                link.classList.add('active');
            }
        });
    }

    // 2. LÓGICA DE NAVEGACIÓN DINÁMICA (History API)
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault(); 
            const categoryKey = this.getAttribute('data-category');
            
            console.log('Click en categoría:', categoryKey);
            
            // Si es la guía, cargar index.php
            if (categoryKey === 'guia') {
                const fetchUrl = 'index.php';
                
                console.log('Cargando guía desde:', fetchUrl);
                
                fetch(fetchUrl)
                    .then(response => {
                        console.log('Respuesta recibida:', response.status);
                        if (!response.ok) {
                            throw new Error('HTTP error ' + response.status);
                        }
                        return response.text();
                    })
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContent = doc.getElementById('wrapper');
                        
                        if (!newContent) {
                            console.error('ERROR: No se encontró #wrapper en la respuesta');
                            return;
                        }
                        
                        wrapper.innerHTML = newContent.innerHTML;
                        window.history.pushState({ category: 'guia' }, 'Guía de Uso', 'index.php');
                        
                        setupCopyListeners();
                        setActiveLink('guia');
                        
                        document.title = doc.querySelector('title')?.textContent || "LINUX | Comandos";
                        console.log('Guía cargada exitosamente');
                    })
                    .catch(error => {
                        console.error('Error cargando la guía:', error);
                        window.location.href = fetchUrl;
                    });
                return;
            }
            
            // Para otras categorías
            const fetchUrl = `categorias.php?cat=${categoryKey}`; 
            const cleanUrl = this.getAttribute('href');
            
            console.log('Cargando categoría desde:', fetchUrl);

            fetch(fetchUrl)
                .then(response => {
                    console.log('Respuesta recibida:', response.status, response.statusText);
                    if (!response.ok) {
                        throw new Error('HTTP error ' + response.status);
                    }
                    return response.text();
                })
                .then(html => {
                    console.log('HTML recibido, tamaño:', html.length, 'caracteres');
                    
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('wrapper');
                    
                    if (!newContent) {
                        console.error('ERROR: No se encontró #wrapper en la respuesta HTML');
                        console.log('HTML recibido:', html.substring(0, 500));
                        return;
                    }
                    
                    console.log('Contenido del wrapper encontrado, tamaño:', newContent.innerHTML.length);
                    
                    wrapper.innerHTML = newContent.innerHTML;
                    
                    window.history.pushState({ category: categoryKey }, document.title, cleanUrl);

                    setupCopyListeners();
                    setActiveLink(categoryKey);
                    
                    document.title = doc.querySelector('title')?.textContent || "LINUX | Comandos";
                    
                    console.log('Categoría cargada exitosamente:', categoryKey);
                })
                .catch(error => {
                    console.error('Error cargando la categoría:', error);
                    window.location.href = fetchUrl;
                });
        });
    });

    // 3. MANEJAR BOTONES ATRÁS/ADELANTE (Popstate)
    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.category) {
            const categoryKey = e.state.category;
            
            console.log('Popstate - navegando a:', categoryKey);
            
            if (categoryKey === 'guia') {
                fetch('index.php')
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContent = doc.getElementById('wrapper');
                        
                        if (newContent) {
                            wrapper.innerHTML = newContent.innerHTML;
                            document.title = doc.querySelector('title')?.textContent || "LINUX | Comandos";
                            setupCopyListeners();
                            setActiveLink('guia');
                        }
                    })
                    .catch(error => console.error('Error al retroceder:', error));
                return;
            }
            
            const fetchUrl = `categorias.php?cat=${categoryKey}`;
            
            fetch(fetchUrl)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('wrapper');
                    
                    if (newContent) {
                        wrapper.innerHTML = newContent.innerHTML;
                        document.title = doc.querySelector('title')?.textContent || "LINUX | Comandos";
                        setupCopyListeners();
                        setActiveLink(categoryKey);
                    }
                })
                .catch(error => console.error('Error al retroceder:', error));
        } else {
            window.location.href = 'index.php';
        }
    });

    // 4. Establecer el enlace activo al cargar la página
    const currentPath = window.location.pathname.split('/').pop();
    if (currentPath === '' || currentPath === 'index.php') {
        setActiveLink('guia');
    } else {
        const categoryMatch = currentPath.match(/([^\/]+)$/);
        if (categoryMatch && categoryMatch[1]) {
            setActiveLink(categoryMatch[1]);
        }
    }
}

// Evento de inicio principal
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM cargado, inicializando...');
    setupCopyListeners();
    setupNavigationAndHamburger();
    console.log('Inicialización completa');
});
