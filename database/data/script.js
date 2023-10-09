const dropZone = document.getElementById('dropZone')
const fileInput = document.getElementById('data')
const fileDisplayZone = document.getElementById('fileDisplayZone')
const fileTypeIcon = document.getElementById('fileTypeIcon')
const fileName = document.getElementById('fileName')
const statusLabel = document.getElementById('statusLabel')

const save = document.getElementById('save')
const saveLabel = document.getElementById('saveLabel')
const saveSpinner = document.getElementById('saveSpinner')

var ajaxRequest

fileInput.value = ''

function handleDragOver(event) {
  event.preventDefault()
  dropZone.classList.add('drag-over')
  event.dataTransfer.dropEffect = 'copy'
}

function handleDragLeave(event) {
  event.preventDefault()
  dropZone.classList.remove('drag-over')

  // Hide drop zone & display display zone if file is already uploaded
  if (fileInput.files.length > 0) {
    dropZone.classList.add('invisible')
    fileDisplayZone.classList.remove('invisible')
  }
}

function handleDrop(event) {
  event.preventDefault()
  dropZone.classList.remove('drag-over')
  const file = event.dataTransfer.files[0]
  const fileExtension = file.name.split('.').pop()

  // Check file type is .txt or .csv
  if (fileExtension === 'txt' || fileExtension === 'csv') {
    fileInput.files = event.dataTransfer.files

    showFileDetails(file)

    // Read file content
    // const reader = new FileReader()

    // reader.onload = function (e) {
    //   const fileContent = e.target.result
    //   console.log(fileContent) // or do something else with the content
    // }

    // reader.readAsText(fileInput.files[0])
  } else {
    alert('Please upload a .txt or .csv file')
  }
}

function dragOverDisplay(event) {
  event.preventDefault()
  dropZone.classList.remove('invisible')
  fileDisplayZone.classList.add('invisible')
}

function removeFile() {
  fileInput.value = ''
  dropZone.classList.remove('invisible')
  fileDisplayZone.classList.add('invisible')
}

function handleFileInput(event) {
  const file = event.target.files[0]
  showFileDetails(file)
}

function showFileDetails(file) {
  const fileExtension = file.name.split('.').pop()

  // Hide drop zone & display display zone
  dropZone.classList.add('invisible')
  fileDisplayZone.classList.remove('invisible')

  // Display file type icon
  if (fileExtension === 'txt') {
    fileTypeIcon.src = './assets/txt.png'
  } else if (fileExtension === 'csv') {
    fileTypeIcon.src = './assets/csv.png'
  }

  // Display file name
  fileName.textContent = file.name
}

function checkFileField() {
  if (fileInput.files.length > 0) {
    return true
  }
  dropZone.classList.add('emptyFile')
  setTimeout(() => {
    dropZone.classList.remove('emptyFile')
  }, 1000)
  alert('Please upload a file')
}

async function saveData(event) {
  event.preventDefault()

  const fileContent = await readFileAsync(fileInput.files[0])
  const nbrRecipients = fileContent.split('\n').length

  const formData = new FormData(event.target)
  formData.set('data', fileContent)
  formData.append('nbrRecipients', nbrRecipients)

  const data = Object.fromEntries(formData)
  // console.log(data)

  save.disabled = true
  saveLabel.classList.add('d-none')
  saveSpinner.classList.remove('d-none')

  try {
    ajaxRequest = $.ajax({
      type: 'POST',
      url: 'http://45.145.6.18/database/data/php/save_data.php',
      data: formData,
      contentType: false, // Set contentType to false, as FormData already sets it to 'multipart/form-data'
      processData: false, // Set processData to false, as FormData already processes the data
      xhr: function () {
        var xhr = new window.XMLHttpRequest()

        xhr.upload.addEventListener(
          'progress',
          function (evt) {
            if (evt.lengthComputable) {
              var percentComplete = evt.loaded / evt.total
              percentComplete = parseInt(percentComplete * 100)
              statusLabel.textContent = `Progress: ${percentComplete}%`

              if (percentComplete === 100) {
                statusLabel.textContent = 'Saving data...'
              }
            }
          },
          false
        )

        return xhr
      },
      success: function (response) {
        console.log(response)
        statusLabel.textContent = response.message

        setTimeout(() => {
          statusLabel.textContent = ''
        }, 10000)

        if (response.status === 'success') {
          statusLabel.classList.remove('text-danger')
          statusLabel.classList.add('text-success')
        } else {
          statusLabel.classList.remove('text-success')
          statusLabel.classList.add('text-danger')
        }

        save.disabled = false
        saveLabel.classList.remove('d-none')
        saveSpinner.classList.add('d-none')

        // See js/dataOps.js
        getData()
      },
    })
  } catch (error) {
    console.error(error)
  }
}

function abortAjaxRequest() {
  if (ajaxRequest) {
    ajaxRequest.abort()
    console.log('Ajax request aborted')

    statusLabel.textContent = 'Insert request aborted'

    setTimeout(() => {
      statusLabel.textContent = ''
    }, 10000)

    statusLabel.className = 'fw-semibold text-secondary'

    save.disabled = false
    saveLabel.classList.remove('d-none')
    saveSpinner.classList.add('d-none')
  } else {
    console.log('No ajax request to abort')
  }
}

async function readFileAsync(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()

    reader.onload = function (e) {
      const fileContent = e.target.result
      resolve(fileContent)
    }

    reader.onerror = function (e) {
      reject(e.target.error)
    }

    reader.readAsText(file)
  })
}
