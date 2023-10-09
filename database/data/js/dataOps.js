const dataTable = document.getElementById('dataTable')
const loadingSpinner = document.getElementById('loadingSpinner')
const notFound = document.getElementById('notFound')
const error = document.getElementById('error')

const trDataExample = document.getElementById('trDataExample')
window.onload = getData()

async function getData() {
  const result = await fetch('https://45.145.6.18/database/data/php/data.php')
  const data = await result.json()
  console.log(data)

  displayData(data)
}

function displayData(data) {
  dataTable.querySelectorAll('.trData').forEach((element) => {
    element.remove()
  })
  if (data.status === 'success') {
    data.data.forEach((element) => {
      let trData = trDataExample.cloneNode(true)
      trData.id = element.id
      trData.setAttribute('data-name', element.name)
      trData.setAttribute('data-country', element.countryName)
      trData.classList.add('trData')

      let tdDataFields = trData.querySelectorAll('td')

      tdDataFields[0].textContent = element.id
      tdDataFields[1].textContent = element.name
      tdDataFields[2].textContent = element.countryName
      tdDataFields[3].textContent = element.nbrRecipients

      trData.classList.remove('d-none')
      trDataExample.parentElement.appendChild(trData)

      dataTable.classList.remove('d-none')
      loadingSpinner.classList.add('d-none')
      notFound.classList.add('d-none')
      error.classList.add('d-none')
    })
  } else if (data.status === 'not found') {
    dataTable.classList.add('d-none')
    loadingSpinner.classList.add('d-none')
    notFound.classList.remove('d-none')
    error.classList.add('d-none')
  } else {
    dataTable.classList.add('d-none')
    loadingSpinner.classList.add('d-none')
    notFound.classList.add('d-none')
    error.classList.remove('d-none')
  }
}

function deleteData(deleteButton) {
  let prompt = confirm('Are you sure you want to delete this data?')
  if (!prompt) return

  deleteButton.disabled = true
  deleteButton.children[0].classList.add('d-none')
  deleteButton.children[1].classList.remove('d-none')

  let id = deleteButton.parentElement.parentElement.id
  // console.log(id)

  try {
    $.ajax({
      type: 'DELETE',
      url: `https://45.145.6.18/database/data/php/data.php?id=${id}`,
      success: function (response) {
        console.log(response)
        getData()

        deleteButton.disabled = false
        deleteButton.children[0].classList.remove('d-none')
        deleteButton.children[1].classList.add('d-none')
      },
    })
  } catch (error) {
    console.error(error)
  }
}

function downloadData(downloadButton) {
  let id = downloadButton.parentElement.parentElement.id
  let name = downloadButton.parentElement.parentElement.getAttribute('data-name')
  let country = downloadButton.parentElement.parentElement.getAttribute('data-country')
  // console.log(id)

  downloadButton.disabled = true
  downloadButton.children[0].classList.add('d-none')
  downloadButton.children[1].classList.remove('d-none')

  try {
    $.ajax({
      type: 'GET',
      url: `https://45.145.6.18/database/data/php/download_data.php?id=${id}`,
      success: function (response) {
        // console.log(response)
        const blob = new Blob([response.data], { type: 'text/plain' }) // Create a Blob from the response data

        const url = URL.createObjectURL(blob)
        const a = document.createElement('a')
        a.href = url
        a.download = `${name}_${country}.txt` // Set the file
        a.click()
        URL.revokeObjectURL(url)
        a.remove()

        downloadButton.disabled = false
        downloadButton.children[0].classList.remove('d-none')
        downloadButton.children[1].classList.add('d-none')
      },
    })
  } catch (error) {
    console.error(error)
  }
}
