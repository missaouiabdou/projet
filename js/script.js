// Sélectionner tous les boutons de type d'utilisateur
// const userTypeButtons = document.querySelectorAll('.user-type-btn');

// // Ajouter des écouteurs d'événements pour chaque bouton
// userTypeButtons.forEach(button => {
//   button.addEventListener('click', () => {
//     const userType = button.dataset.form;
//     window.location.href = `../html/${userType}.html`;
//   });
// });

document.addEventListener('DOMContentLoaded', function() {
  const userCards = document.querySelectorAll('.user-card');
  
  // Create ripple effect on click
  function createRipple(event) {
    const card = event.currentTarget;
    const circle = document.createElement('span');
    const rect = card.getBoundingClientRect();
    const diameter = Math.max(rect.width, rect.height);
    const radius = diameter / 2;
    
    circle.style.width = circle.style.height = `${diameter}px`;
    circle.style.left = `${event.clientX - rect.left - radius}px`;
    circle.style.top = `${event.clientY - rect.top - radius}px`;
    circle.classList.add('ripple');
    
    const ripple = card.getElementsByClassName('ripple')[0];
    if (ripple) {
      ripple.remove();
    }
    
    card.appendChild(circle);
  }
  
  userCards.forEach(card => {
    card.addEventListener('click', function(e) {
      createRipple(e);
      const formType = this.getAttribute('data-form');
      
      // Add glow effect
      this.classList.add('glow');
      if (formType === 'encadrant') this.classList.add('glow-purple');
      if (formType === 'admin') this.classList.add('glow-orange');
      
      setTimeout(() => {
        this.classList.remove('glow', 'glow-purple', 'glow-orange');
        // In a real application, you would redirect to the appropriate login form
        // window.location.href = `/login/${formType}`;
        
        // For demo purposes, show a modal instead
        showLoginModal(formType);
      }, 500);
    });
    
    // Enhanced hover effects
    card.addEventListener('mouseenter', function() {
      this.style.boxShadow = `0 10px 25px -5px rgba(0, 0, 0, 0.3)`;
    });
    
    card.addEventListener('mouseleave', function() {
      this.style.boxShadow = '';
    });
  });
  
  // Add focus styles for accessibility
  userCards.forEach(card => {
    card.addEventListener('focus', function() {
      this.style.outline = '2px solid rgba(59, 130, 246, 0.5)';
      this.style.outlineOffset = '2px';
    });
    
    card.addEventListener('blur', function() {
      this.style.outline = '';
    });
  });
  
  // Language selector functionality
  const languageSelectors = document.querySelectorAll('.language-selector > div');
  languageSelectors.forEach(selector => {
    selector.addEventListener('click', function() {
      // Remove active class from all selectors
      languageSelectors.forEach(s => s.classList.remove('bg-blue-500/30', 'bg-purple-500/30', 'bg-amber-500/30'));
      
      // Add active class to clicked selector
      if (this.textContent.trim() === 'FR') {
        this.classList.add('bg-blue-500/30');
      } else if (this.textContent.trim() === 'EN') {
        this.classList.add('bg-purple-500/30');
      } else {
        this.classList.add('bg-amber-500/30');
      }
      
      // In a real app, you would change the language here
      console.log('Language changed to:', this.textContent.trim());
    });
  });
  
  // Show login modal (demo purposes)
  function showLoginModal(role) {
    // Create modal elements
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
    modal.id = 'login-modal';
    
    const modalContent = document.createElement('div');
    modalContent.className = 'bg-slate-800 rounded-xl p-8 max-w-md w-full relative';
    modalContent.innerHTML = `
      <button class="absolute top-4 right-4 text-slate-400 hover:text-white transition-colors">
        <i class="fas fa-times"></i>
      </button>
      <div class="text-center mb-6">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full ${role === 'stagiaire' ? 'bg-blue-500/20' : role === 'encadrant' ? 'bg-purple-500/20' : 'bg-amber-500/20'} flex items-center justify-center">
          <i class="fas ${role === 'stagiaire' ? 'fa-user-graduate' : role === 'encadrant' ? 'fa-user-tie' : 'fa-user-shield'} text-2xl ${role === 'stagiaire' ? 'text-blue-400' : role === 'encadrant' ? 'text-purple-400' : 'text-amber-400'}"></i>
        </div>
        <h3 class="text-xl font-semibold text-white mb-1">Connexion ${role.charAt(0).toUpperCase() + role.slice(1)}</h3>
        <p class="text-slate-400 text-sm">Entrez vos identifiants pour continuer</p>
      </div>
      <form class="space-y-4" action="login.php" method="POST">
        <div>
          <label for="email" class="block text-sm font-medium text-slate-300 mb-1">Email</label>
          <input type="email" id="email" class="w-full bg-slate-700/50 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 ${role === 'stagiaire' ? 'focus:ring-blue-500' : role === 'encadrant' ? 'focus:ring-purple-500' : 'focus:ring-amber-500'} focus:border-transparent transition-all" placeholder="votre@email.com">
        </div>
        <div>
          <label for="password" class="block text-sm font-medium text-slate-300 mb-1">Mot de passe</label>
          <input type="password" id="password" class="w-full bg-slate-700/50 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 ${role === 'stagiaire' ? 'focus:ring-blue-500' : role === 'encadrant' ? 'focus:ring-purple-500' : 'focus:ring-amber-500'} focus:border-transparent transition-all" placeholder="••••••••">
        </div>
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input type="checkbox" id="remember" class="h-4 w-4 ${role === 'stagiaire' ? 'text-blue-500' : role === 'encadrant' ? 'text-purple-500' : 'text-amber-500'} focus:ring-0 rounded border-slate-500">
            <label for="remember" class="ml-2 block text-sm text-slate-400">Se souvenir de moi</label>
          </div>
          <a href="#" class="text-sm ${role === 'stagiaire' ? 'text-blue-400 hover:text-blue-300' : role === 'encadrant' ? 'text-purple-400 hover:text-purple-300' : 'text-amber-400 hover:text-amber-300'} transition-colors">Mot de passe oublié?</a>
        </div>
        <button type="submit" class="w-full ${role === 'stagiaire' ? 'bg-blue-600 hover:bg-blue-700' : role === 'encadrant' ? 'bg-purple-600 hover:bg-purple-700' : 'bg-amber-600 hover:bg-amber-700'} text-white font-medium py-2 px-4 rounded-lg transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-800 ${role === 'stagiaire' ? 'focus:ring-blue-500' : role === 'encadrant' ? 'focus:ring-purple-500' : 'focus:ring-amber-500'}">
          Se connecter
        </button>
      </form>
      <div class="mt-4 text-center text-sm text-slate-500">
        Nouveau sur la plateforme? <a href="#" class="${role === 'stagiaire' ? 'text-blue-400 hover:text-blue-300' : role === 'encadrant' ? 'text-purple-400 hover:text-purple-300' : 'text-amber-400 hover:text-amber-300'} transition-colors">Créer un compte</a>
      </div>
    `;
    
    modal.appendChild(modalContent);
    document.body.appendChild(modal);
    
    // Close modal when clicking X or outside
    modal.addEventListener('click', function(e) {
      if (e.target === modal || e.target.closest('button')) {
        modal.remove();
      }
    });
  }
});
