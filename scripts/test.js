window.onload = function() {
  select_input = document.getElementsByClassName('select-input')[0];
  search_bar = document.getElementsByClassName('search-bar')[0];
  dropdown = document.getElementsByClassName('dropdown')[0];
  console.log(select_input);

  // Listen for click events on the document object
  document.addEventListener('click', (event) => {
    // If the click event didn't occur inside the dropdown or the input, hide the dropdown
    if (!dropdown.contains(event.target) && !select_input.contains(event.target)) {
      dropdown.style.display = 'none';
      search_bar.classList.remove('dropdown-active');
    }
  });

  // Show the dropdown when the input is in focus

  search_bar.addEventListener('focus', () => {
    dropdown.style.display = 'flex';
    search_bar.classList.add('dropdown-active');
  });

  dropdown.addEventListener('focus', () => {
    dropdown.style.display = 'flex';
    search_bar.classList.add('dropdown-active');
  });
};




