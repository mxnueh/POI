document.addEventListener('DOMContentLoaded', () => {
  const toggleButton = document.getElementById('toggle-btn')
  const sidebar = document.getElementById('sidebar')

  // Recuperar el estado guardado del sidebar
  const savedState = localStorage.getItem('sidebarState')
  if (savedState === 'closed') {
    sidebar.classList.add('close')
    toggleButton.classList.add('rotate')
  } else {
    sidebar.classList.remove('close')
    toggleButton.classList.remove('rotate')
  }

  // Función para alternar el sidebar y guardar el estado
  function toggleSidebar() {
    sidebar.classList.toggle('close')
    toggleButton.classList.toggle('rotate')

    // Guardar el estado en localStorage
    localStorage.setItem('sidebarState', sidebar.classList.contains('close') ? 'closed' : 'open')

    closeAllSubMenus()
  }

  // Asignar evento de clic al botón de toggle
  toggleButton.addEventListener('click', toggleSidebar)

})

