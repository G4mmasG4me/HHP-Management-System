window.onload = function() {
  generateStartTimes(0, 1425);
  generateEndTimes(15, 1425);
  positionTopLeftCalendar();
  currentTime();
};

window.addEventListener("resize", positionTopLeftCalendar);
interval = setInterval(currentTime, 1000)

// time = 'y-m-d h-m-s'

// ----- Add Work Functions -----
function open_add_work_popout() {
  document.getElementById('cover-container').style.display = 'flex';
  document.getElementById('add-work').style.display = 'flex';
}

function close_add_work_popout() {
  document.getElementById('cover-container').style.display = 'none';
  document.getElementById('add-work').style.display = 'none';
}

// adds selected customer
function add_customer(customer) {
  selected_customer = customer.parentElement.parentElement.getElementsByClassName('selected-customer')[0];
  selected_customer.children[0].text = customer.children[0].text // adds selected customer name
  selected_customer.children[1].text = customer.children[1].text // adds selected customer address
  selected_customer.children[2].value = customer.children[2].value // adds selected customer id

  customer.parentElement.style.display = 'none';
  customer.parentElement.parentElement.querySelector('label[for="customer_search"]').style.display = 'none';
  customer.parentElement.parentElement.querySelector('input[name="customer_search"]').style.display = 'none';
  customer.parentElement.parentElement.querySelector('input[name="customer_search"]').value = '';
  customer.parentElement.innerHTML = '';
}

// removes selected_customer
function remove_customer(selected_customer) {
  selected_customer.parentElement.querySelector('label[for="customer_search"]').style.display = 'block';
  selected_customer.parentElement.querySelector('input[name="customer_search"]').style.display = 'block';
  selected_customer.parentElement.getElementsByClassName('customer-search-results')[0].style.display = 'flex';
  selected_customer.children[0].children[0].text = '';
  selected_customer.children[0].children[1].text = '';
  selected_customer.children[1].value = '';
  selected_customer.style.display = 'none';

}

function openChangeWorkPopup(position) {
  id = 'work-' + String(position);
  document.getElementById('cover-container').style.display = 'flex';
  document.getElementById(id).style.display = 'flex';

}
function closeChangeWorkPopup(element) {
  document.getElementById('cover-container').style.display = 'none';
  element.parentNode.parentNode.style.display = 'none';
}

function changeWorkPopoutContent(element) {
  index = Array.from(element.parentNode.children).indexOf(element);
  // hide all elements
  content = Array.from(element.parentNode.parentNode.getElementsByClassName('work-popout-content')[0].children);
  for(let i = 0; i<content.length; i++) {
    content[i].style.display = 'none';
  }
  // show selected
  content[index].style.display = 'flex';

}

function editableText(element) {
  element.readOnly = false;
}

function updateNote(input) {
  var xhttp;
  note = input.parentElement
  id = note.getElementsByClassName('note_id')[0].value;
  input_name = input.name;
  input_value = input.value;
  url_data = 'id=' + id + '&' + input_name + '=' + input_value;
  console.log('updating note');
  console.log(url_data);
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) { // on completition
      console.log(this.responseText);
      // could output new data
      // change input to readonly
      input.readOnly = true;
    }
  };
  xhttp.open('GET', 'update_note.php?'+url_data, true);
  xhttp.send();
}

function addNote(notes) {
  work_id = notes.parentNode.parentNode.parentNode.id.split('-')[1];
  console.log(work_id);
  var xhttp;
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) { // on completition
      // add new note to notes
      notes.innerHTML = this.responseText;
    }
  };
  xhttp.open('GET', 'add_note.php?work_id='+work_id, true)
  xhttp.send();
}

function test(element) {
  console.log(element.parentElement.parentElement.innerHTML);
}

function searchProduct(search_bar) {
  query = search_bar.value
  if (query) {
    var xhttp;
    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) { // on completition
        search_bar.parentElement.getElementsByClassName('search-results')[0].innerHTML = this.responseText;
      }
    };
    xhttp.open('GET', 'search_product.php?query='+query, true)
    xhttp.send();
  }
  else {
    search_bar.parentElement.getElementsByClassName('search-results')[0].innerHTML = '';
  }

}

function add_product(product_element) {
  product_id = product_element.querySelector('input[name="product_id"]').value;
  work_id = product_element.parentElement.parentElement.parentElement.parentElement.querySelector('input[name="work_id"]').value;
  var xhttp;
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) { // on completition
      product_element.parentElement.parentElement.innerHTML = this.responseText;
      product_element.parentElement.parentElement.getElementsByClassName('search-bar')[0].value = '';
      product_element.parentElement.innerHTML = '';
    }
  };
  xhttp.open('GET', 'add_receipt_product.php?work_id='+work_id+'&product_id='+product_id, true)
  xhttp.send();
}

function change_quantity(product_element, changeby) {
  product_id = product_element.querySelector('input[name="product_id"]').value;
  work_id = product_element.parentElement.parentElement.parentElement.parentElement.querySelector('input[name="work_id"]').value;
  var xhttp;
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) { // on completition
      product_element.parentElement.parentElement.innerHTML = this.responseText;
    }
  };
  xhttp.open('GET', 'change_quantity.php?work_id='+work_id+'&product_id='+product_id+'&changeby='+changeby, true);
  xhttp.send();
}

function change_adjustment(adjustment_element, changeby) {
  
  work_id = adjustment_element.parentElement.parentElement.parentElement.parentElement.parentElement.querySelector('input[name="work_id"]').value;
  console.log('Change Adjustment, ' + changeby + ', work id: ' + work_id);
  var xhttp;
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) { // on completition
      adjustment_element.parentElement.parentElement.parentElement.innerHTML = this.responseText;
    }
  };
  xhttp.open('GET', 'change_adjustment.php?work_id='+work_id+'&changeby='+changeby, true);
  xhttp.send();
}

function change_adjustment_exact(adjustment_element) {
  adjustment = adjustment_element.value;
  work_id = adjustment_element.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.querySelector('input[name="work_id"]').value;
  var xhttp;
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) { // on completition
      adjustment_element.parentElement.parentElement.parentElement.parentElement.innerHTML = this.responseText;
    }
  };
  xhttp.open('GET', 'change_adjustment_exact.php?work_id='+work_id+'&adjustment='+adjustment, true);
  xhttp.send();
}

function change_adjusted_price(adjusted_price_element) {
  adjusted_price = adjusted_price_element.value;
  work_id = adjusted_price_element.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.querySelector('input[name="work_id"]').value;
  console.log(adjusted_price);
  console.log(work_id);
  var xhttp;
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) { // on completition
      console.log(this.responseText);
      adjusted_price_element.parentElement.parentElement.parentElement.parentElement.innerHTML = this.responseText;
    }
  };
  xhttp.open('GET', 'change_adjusted_price.php?work_id='+work_id+'&adjusted_price='+adjusted_price, true);
  xhttp.send();
}














// Add Work Functions

function search_work_type(work_type_input) {
  search_query = work_type_input.value;
  var xhttp;
  if (search_query == '') { // sets search result to empty
    work_type_input.parentElement.nextSibling.innerHTML = '';
    return;
  }
  xhttp = new XMLHttpRequest();;
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      work_type_input.parentElement.nextElementSibling.innerHTML = this.responseText;
    }
  };
  xhttp.open('GET', 'search_work_type.php?q='+search_query, true)
  xhttp.send();
}

function add_work_type(work_type) {
  selected_work_type = work_type.parentElement.nextElementSibling;
  search_results = work_type.parentElement
  search_input = work_type.parentElement.previousElementSibling.children[1]

  // set selected address values
  selected_work_type.children[0].children[0].textContent = work_type.children[0].children[0].textContent
  selected_work_type.children[0].children[1].textContent = work_type.children[0].children[1].textContent
  selected_work_type.children[0].children[2].textContent = work_type.children[0].children[2].textContent
  selected_work_type.children[2].value = work_type.children[1].value

  // show selected address
  selected_work_type.style.display = 'flex';

  // empty search results
  search_results.innerHTML = '';
  // empty search query
  search_input.value = '';
  
  // hide search result
  search_results.style.display = 'none';
  // hide search query
  search_results.previousElementSibling.style.display = 'none';
}

function remove_work_type(selected_work_type) {
  // remove selected address values
  selected_work_type.children[0].children[0].text = '';
  selected_work_type.children[0].children[1].text = '';
  selected_work_type.children[0].children[2].text = '';
  selected_work_type.children[2].value = '';

  // hide selected address
  selected_work_type.style.display = 'none';

  // show search results
  selected_work_type.previousElementSibling.style.display = 'flex';

  // show search query
  selected_work_type.previousElementSibling.previousElementSibling.style.display = 'flex';
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
  selected_customer.children[0].children[0].textContent = customer.children[0].children[0].textContent
  selected_customer.children[0].children[1].textContent = customer.children[0].children[1].textContent
  selected_customer.children[0].children[2].textContent = customer.children[0].children[2].textContent
  selected_customer.children[2].value = customer.children[1].value

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
  selected_address.children[0].children[0].textContent = address.children[0].children[0].textContent
  selected_address.children[0].children[1].textContent = address.children[0].children[1].textContent
  selected_address.children[0].children[2].textContent = address.children[0].children[2].textContent
  selected_address.children[0].children[3].textContent = address.children[0].children[3].textContent
  selected_address.children[2].value = address.children[1].value

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

function setLowestDateTime(time_input) {
  d = new Date();
  timezone_diff = d.getTimezoneOffset();
  
  start_day = time_input.children[1];
  start_time = time_input.children[2];
  end_day = time_input.nextElementSibling.children[1];
  end_time = time_input.nextElementSibling.children[2];
  current_end_time_value = end_time.value;

  // if start day set
  if (start_day.value) {
    start_day_values = start_day.value.split('-');
    start_time_values = start_time ? start_time.value.split(':') : ['00','00']
    utc_current = Date.UTC(start_day_values[0], start_day_values[1]-1, start_day_values[2], start_time_values[0], start_time_values[1])
    min_datetime = new Date(utc_current + 15*60*1000); // add 15 mins and create date object
    min_datetime_iso = min_datetime.toISOString();
    min_day = min_datetime_iso.split('T')[0]
    min_time = min_datetime_iso.split('T')[1]
    min_time = min_time.substring(0, min_time.length-5);
    end_day.min = min_day;

    // generate all times between min time and next day time
    // get max time range in minutes
    max_unix = 23*60 + 45;
    hours = parseInt(min_time.split(':')[0]);
    mins = parseInt(min_time.split(':')[1]);

    start_unix = hours * 60 + mins;
    
    generateEndTimes(start_unix, max_unix);
    if (end_time.querySelector('option[value="'+current_end_time_value+'"]')) {
      end_time.value = current_end_time_value;
    }
    else {
      end_time.value = end_time.children[0].value
    }
  }
  else {
    end_day.min = '';
  }
}


function setHighestDateTime(time_input) {
  // if start day set
    // min day == start day + (start_time or 00:00)
  // start day not set
    // no min day
  d = new Date();
  timezone_diff = d.getTimezoneOffset();
  
  end_day = time_input.children[1];
  end_time = time_input.children[2];
  start_day = time_input.previousElementSibling.children[1];
  start_time = time_input.previousElementSibling.children[2];
  current_start_time_value = start_time.value;

  // if end day set
  if (end_day.value) {
    end_day_values = end_day.value.split('-');
    end_time_values = end_time ? end_time.value.split(':') : ['00','00']
    utc_current = Date.UTC(end_day_values[0], end_day_values[1], end_day_values[2], end_time_values[0], end_time_values[1])
    max_datetime = new Date(utc_current - 15*60*1000);
    max_datetime_iso = max_datetime.toISOString();
    max_day = max_datetime_iso.split('T')[0]
    max_time = max_datetime_iso.split('T')[1]
    max_time = max_time.substring(0, max_time.length-5);

    start_day.max = max_day;

    // generate all times between min time and next day time
    // get max time range in minutes
    start_unix = 0;
    hours = parseInt(max_time.split(':')[0]);
    mins = parseInt(max_time.split(':')[1]);

    max_unix = hours * 60 + mins;
    
    generateStartTimes(start_unix, max_unix);

    // search for current selected value
    if (start_time.querySelector('option[value="'+current_start_time_value+'"]')) {
      start_time.value = current_start_time_value;
    }
    else {
      start_time.value = start_time.children[start_time.children.length-1].value
    }
    
  }
}

function generateStartTimes(start, end) {
  start_time = document.querySelector('select[name="start_time"]');
  start_time.innerHTML = '';
  current_unix = start;
  while (end >= current_unix) {
    hour = String(Math.floor(current_unix / 60));
    min = String(current_unix % 60);
    hour = hour.padStart(2, 0)
    min = min.padStart(2, 0)
    option_node = document.createElement('option');
    time_value = hour + ':' + min
    option_node.value = time_value;
    option_node.innerHTML = time_value;
    start_time.appendChild(option_node);
    current_unix = current_unix + 15;
  }
}

function generateEndTimes(start, end) {
  end_time = document.querySelector('select[name="end_time"]');
  end_time.innerHTML = '';
  current_unix = start;
  while (end >= current_unix) {
    hour = String(Math.floor(current_unix / 60));
    min = String(current_unix % 60);
    hour = hour.padStart(2, 0)
    min = min.padStart(2, 0)
    option_node = document.createElement('option');
    time_value = hour + ':' + min
    option_node.value = time_value;
    option_node.innerHTML = time_value;
    end_time.appendChild(option_node);
    current_unix = current_unix + 15;
  }
}

function positionTopLeftCalendar() {
  console.log('Now')
  times = document.getElementsByClassName('times')[0];
  tl = document.getElementsByClassName('calendar-tl')[0];
  console.log(times.clientWidth);
  console.log(tl)
  tl.style.width = times.getBoundingClientRect().width + 'px';
}

function currentTime() {
  // get week start
  week_start = new Date(document.querySelector('input[name="week_start"]').value);
  week_end = new Date(week_start.getTime() + (6 * 24 * 60 * 60 * 1000));
  // get current date and time
  current_date = new Date();

  day = current_date.getDay();
  total_seconds = ((current_date.getHours() * 60) + current_date.getMinutes()) * 60 + current_date.getSeconds(); // ((hours * 60) + minutes) * 60 + seconds
  margin_top = (total_seconds / 86400 * 100) + '%';
  margin_left = (day * (1/7) * 100) + '%';

  time_element = document.getElementsByClassName('current-time')[0];
  console.log(time_element);
  console.log(margin_left);
  time_element.style.top = margin_top;
  time_element.style.left = margin_left;

  if (current_date >= week_start && current_date <= week_end) {
    // Execute your code here
    time_element.style.display = 'block';
  }
  else {
    time_element.style.display = 'none';
  }
}