

function open_close_sidebar() {
  links = document.getElementsByClassName('links')[0]
  links.style.display = links.style.display == 'none' ? 'flex' : 'none';
}

function open_section(button) {
  pos = Array.from(button.parentElement.children).indexOf(button)
  content = document.getElementsByClassName('content')[0]

  for (i=0;i<content.children.length;i++) {
    content.children[i].style.display = 'none';
  }
  content.children[pos].style.display = 'flex';
}

function search_address(address_input) {
  search_query = address_input.value;
  var xhttp;
  if (search_query == '') { // sets search result to empty
    address_input.parentElement.nextSibling.innerHTML = '';
    return;
  }
  xhttp = new XMLHttpRequest();;
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      address_input.parentElement.nextElementSibling.innerHTML = this.responseText;
    }
  };
  xhttp.open('GET', 'search_address.php?q='+search_query, true)
  xhttp.send();
}

function add_address(address) {
  selected_address = address.parentElement.nextElementSibling;
  search_results = address.parentElement
  search_input = address.parentElement.previousElementSibling.children[1]

  // set selected address values
  console.log(selected_address.children[0].children[0].text)
  console.log(selected_address.children[2])
  console.log(address.children)
  selected_address.children[0].children[0].textContent = address.children[0].textContent
  selected_address.children[0].children[1].textContent = address.children[1].textContent
  selected_address.children[0].children[2].textContent = address.children[2].textContent
  selected_address.children[0].children[3].textContent = address.children[3].textContent
  selected_address.children[2].value = address.children[4].value

  // show selected address
  selected_address.style.display = 'flex';

  // empty search results
  search_results.innerHTML = '';
  // empty search query
  search_input.value = '';
  
  // hide search result
  search_results.style.display = 'none';
  // hide search query
  search_results.previousElementSibling.style.display = 'none';
}

function remove_address(selected_address) {
  // remove selected address values
  selected_address.children[0].children[0].text = '';
  selected_address.children[0].children[1].text = '';
  selected_address.children[0].children[2].text = '';
  selected_address.children[0].children[3].text = '';
  selected_address.children[2].value = '';

  // hide selected address
  selected_address.style.display = 'none';

  // show search results
  selected_address.previousElementSibling.style.display = 'flex';

  // show search query
  selected_address.previousElementSibling.previousElementSibling.style.display = 'flex';
}


function search_customer(customer_input) {
  search_query = customer_input.value;
  var xhttp;
  if (search_query == '') { // sets search result to empty
    customer_input.parentElement.nextSibling.innerHTML = '';
    return;
  }
  xhttp = new XMLHttpRequest();;
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      customer_input.parentElement.nextElementSibling.innerHTML = this.responseText;
    }
  };
  xhttp.open('GET', 'search_customer.php?q='+search_query, true)
  xhttp.send();
}

function add_customer(customer) {
  selected_customer = customer.parentElement.nextElementSibling;
  search_results = customer.parentElement
  search_input = customer.parentElement.previousElementSibling.children[1]

  // set selected address values
  selected_customer.children[0].children[0].textContent = customer.children[0].textContent
  selected_customer.children[0].children[1].textContent = customer.children[1].textContent
  selected_customer.children[0].children[2].textContent = customer.children[2].textContent
  selected_customer.children[2].value = customer.children[3].value

  // show selected address
  selected_customer.style.display = 'flex';

  // empty search results
  search_results.innerHTML = '';
  // empty search query
  search_input.value = '';
  
  // hide search result
  search_results.style.display = 'none';
  // hide search query
  search_results.previousElementSibling.style.display = 'none';

}

function remove_customer(selected_customer) {
  // remove selected customer values
  selected_customer.children[0].children[0].text = '';
  selected_customer.children[0].children[1].text = '';
  selected_customer.children[0].children[2].text = '';
  selected_customer.children[2].value = '';

  // hide selected customer
  selected_customer.style.display = 'none';

  // show search results
  selected_customer.previousElementSibling.style.display = 'flex';

  // show search query
  selected_customer.previousElementSibling.previousElementSibling.style.display = 'flex';
}




