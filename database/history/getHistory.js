var data

window.onload = getHistory()
window.onload = changeSearchType()

async function getHistory() {
    const result = await fetch('http://45.145.6.18/database/history/getOfferHistory.php')

    data = await result.json()

    displayHistory(data)
}

function displayHistory(data) {
    const numberMap = {
        1: 'One',
        2: 'Two',
        3: 'Three',
        4: 'Four',
    }

    const offerHistory = document.getElementById('offer-history')
    offerHistory.innerHTML = ''

    var mailerindex = 1
    var countryindex = 1
    var offerindex = 1

    data.forEach((mailer) => {
        // Mailer Accordion
        // Create Accordion Item and set its class
        const mailerAccordionItem = document.createElement('div')
        mailerAccordionItem.classList.add('accordion-item')

        // Create Accordion Header which is h2 and having a button which will be added to the header
        const mailerAccordionHeader = document.createElement('h2')
        mailerAccordionHeader.classList.add('accordion-header')

        // Create Accordion Button
        const mailerAccordionButton = document.createElement('button')
        mailerAccordionButton.classList.add('accordion-button')
        mailerAccordionButton.classList.add('collapsed')
        mailerAccordionButton.setAttribute('type', 'button')
        mailerAccordionButton.setAttribute('data-bs-toggle', 'collapse')
        mailerAccordionButton.setAttribute('data-bs-target', `#mailerCollapse${mailerindex}`)
        mailerAccordionButton.setAttribute('aria-expanded', `false`)
        mailerAccordionButton.setAttribute('aria-controls', `mailerCollapse${mailerindex}`)
        mailerAccordionButton.innerText = mailer.firstName

        // Add the button to the Accordion header which itself will be added to the accordion item
        mailerAccordionHeader.appendChild(mailerAccordionButton)
        mailerAccordionItem.appendChild(mailerAccordionHeader)

        // Create Accordion Collapse which is div and having a div (Accordion Body) which will have inside it another accordion of countries
        const mailerAccordionCollapse = document.createElement('div')
        mailerAccordionCollapse.id = `mailerCollapse${mailerindex}`
        mailerAccordionCollapse.classList.add('accordion-collapse')
        mailerAccordionCollapse.classList.add('collapse')
        mailerAccordionCollapse.setAttribute('data-bs-parent', '#offer-history')

        const mailerAccordionBody = document.createElement('div')
        mailerAccordionBody.classList.add('accordion-body')
        mailerAccordionBody.classList.add('px-4')
        mailerAccordionBody.innerHTML = '<h4 class="mb-3">Countries:</h4>'

        const countryAccordion = document.createElement('div')
        countryAccordion.classList.add('accordion-country')
        // countryAccordion.classList.add('accordion-flush')
        countryAccordion.id = `countries_${mailer.firstName}`

        mailer.countries.forEach((country) => {
            // Country Accordion
            // Create Accordion Item and set its class
            const countryAccordionItem = document.createElement('div')
            countryAccordionItem.classList.add('accordion-item')

            // Create Accordion Header which is h2 and having a button which will be added to the header
            const countryAccordionHeader = document.createElement('h2')
            countryAccordionHeader.classList.add('accordion-header')

            // Create Accordion Button
            const countryAccordionButton = document.createElement('button')
            countryAccordionButton.classList.add('accordion-button')
            countryAccordionButton.classList.add('country-button')
            countryAccordionButton.classList.add('collapsed')
            countryAccordionButton.setAttribute('type', 'button')
            countryAccordionButton.setAttribute('data-bs-toggle', 'collapse')
            countryAccordionButton.setAttribute('data-bs-target', `#country_${mailer.firstName}Collapse${countryindex}`)
            countryAccordionButton.setAttribute('aria-expanded', `false`)
            countryAccordionButton.setAttribute('aria-controls', `country_${mailer.firstName}Collapse${countryindex}`)
            countryAccordionButton.innerText = country.name

            // Add the button to the Accordion header which itself will be added to the accordion item
            countryAccordionHeader.appendChild(countryAccordionButton)
            countryAccordionItem.appendChild(countryAccordionHeader)

            // Create Accordion Collapse which is div and having a div (Accordion Body) which will have inside it another accordion of countries
            const countryAccordionCollapse = document.createElement('div')
            countryAccordionCollapse.id = `country_${mailer.firstName}Collapse${countryindex}`
            countryAccordionCollapse.classList.add('accordion-collapse')
            countryAccordionCollapse.classList.add('collapse')
            countryAccordionCollapse.setAttribute('data-bs-parent', `#countries_${mailer.firstName}`)

            const countryAccordionBody = document.createElement('div')
            countryAccordionBody.classList.add('accordion-body')
            countryAccordionBody.classList.add('px-4')
            countryAccordionBody.innerHTML = '<h5 class="mb-3">Offers:</h5>'

            const offerAccordion = document.createElement('div')
            offerAccordion.classList.add('accordion-offer')
            // offerAccordion.classList.add('accordion-flush')
            offerAccordion.id = `offers_${mailer.firstName}_${country.name.replace(' ', '_')}`

            country.offers.forEach((offer) => {
                // Offer Accordion
                // Create Accordion Item and set its class
                const offerAccordionItem = document.createElement('div')
                offerAccordionItem.classList.add('accordion-item')

                // Create Accordion Header which is h2 and having a button which will be added to the header
                const offerAccordionHeader = document.createElement('h2')
                offerAccordionHeader.classList.add('accordion-header')

                // Create Accordion Button
                const offerAccordionButton = document.createElement('button')
                offerAccordionButton.classList.add('accordion-button')
                offerAccordionButton.classList.add('offer-button')
                offerAccordionButton.classList.add('collapsed')
                offerAccordionButton.setAttribute('type', 'button')
                offerAccordionButton.setAttribute('data-bs-toggle', 'collapse')
                offerAccordionButton.setAttribute('data-bs-target', `#offer_${mailer.firstName}_${country.name.replace(' ', '_')}Collapse${offerindex}`)
                offerAccordionButton.setAttribute('aria-expanded', `false`)
                offerAccordionButton.setAttribute('aria-controls', `offer_${mailer.firstName}_${country.name.replace(' ', '_')}Collapse${offerindex}`)

                const offerTitle = document.createElement('div')
                offerTitle.classList.add('d-flex')
                offerTitle.classList.add('justify-content-between')
                offerTitle.classList.add('w-100')
                offerTitle.innerHTML = `<span>${offer.offerName}</span>
                                        <span class="pe-3">${offer.date}</span>`

                offerAccordionButton.appendChild(offerTitle)

                // Add the button to the Accordion header which itself will be added to the accordion item
                offerAccordionHeader.appendChild(offerAccordionButton)
                offerAccordionItem.appendChild(offerAccordionHeader)

                // Create Accordion Collapse which is div and having a div (Accordion Body) which will have inside it another accordion of countries
                const offerAccordionCollapse = document.createElement('div')
                offerAccordionCollapse.id = `offer_${mailer.firstName}_${country.name.replace(' ', '_')}Collapse${offerindex}`
                offerAccordionCollapse.classList.add('accordion-collapse')
                offerAccordionCollapse.classList.add('collapse')
                offerAccordionCollapse.setAttribute('data-bs-parent', `#offers_${mailer.firstName}_${country.name.replace(' ', '_')}`)

                const offerAccordionBody = document.createElement('div')
                offerAccordionBody.classList.add('accordion-body')
                offerAccordionBody.classList.add('px-4')
                offerAccordionBody.id = offer.id
                offerAccordionBody.innerHTML = `Offer ID: <span class="fw-semibold">${offer.offerID}</span><br>
                                                Offer name: <span class="fw-semibold">${offer.offerName}</span><br>
                                                Date: <span class="fw-semibold">${offer.date}</span>`

                const offerOptions = document.createElement('div')
                offerOptions.classList.add('mt-3')
                offerOptions.classList.add('d-flex')
                offerOptions.classList.add('justify-content-end')

                const offerButtonsContainer = document.createElement('div')
                offerButtonsContainer.classList.add('mt-3')
                offerButtonsContainer.classList.add('w-auto')
                offerButtonsContainer.classList.add('d-flex')
                offerButtonsContainer.classList.add('gap-2')

                const downloadOfferButton = document.createElement('button')
                downloadOfferButton.classList.add('btn')
                downloadOfferButton.classList.add('btn-primary')
                downloadOfferButton.innerText = 'Download offer'
                downloadOfferButton.addEventListener('click', function () {
                    downloadOffer(offer.id, offer.offerID, offer.offerName, offer.date)
                })

                const loadOfferButton = document.createElement('button')
                loadOfferButton.classList.add('btn')
                loadOfferButton.classList.add('btn-success')
                loadOfferButton.innerText = 'Load offer'

                offerButtonsContainer.appendChild(downloadOfferButton)
                offerButtonsContainer.appendChild(loadOfferButton)

                offerOptions.appendChild(offerButtonsContainer)

                offerAccordion.appendChild(offerAccordionItem)
                offerAccordionItem.appendChild(offerAccordionCollapse)
                offerAccordionCollapse.appendChild(offerAccordionBody)
                offerAccordionBody.appendChild(offerOptions)

                countryAccordion.appendChild(countryAccordionItem)
                countryAccordionItem.appendChild(countryAccordionCollapse)
                countryAccordionCollapse.appendChild(countryAccordionBody)

                countryAccordionBody.appendChild(offerAccordion)

                offerindex = offerindex + 1
            })
            offerindex = 1

            mailerAccordionItem.appendChild(mailerAccordionCollapse)
            mailerAccordionCollapse.appendChild(mailerAccordionBody)

            mailerAccordionBody.appendChild(countryAccordion)

            countryindex = countryindex + 1
        })
        countryindex = 1

        offerHistory.appendChild(mailerAccordionItem)

        mailerindex = mailerindex + 1
    })
}

function changeSearchType() {
    const offerAttr = document.getElementById('offerAttr').value
    let searchField = document.getElementById('search')

    searchField.value = ''

    if (offerAttr == 'date') {
        searchField.type = 'date'
    } else {
        searchField.type = 'search'
    }
}

function searchOffer() {
    let searchTerm = document.getElementById('search').value
    const offerAttr = document.getElementById('offerAttr').value

    const filteredData = data.filter((mailer) => {
        return mailer.countries.some((country) => {
            return country.offers.some((offer) => {
                return offer[offerAttr].toLowerCase().includes(searchTerm.toLowerCase())
            })
        })
    })

    displayHistory(filteredData)
}

async function downloadOffer(id, offerID, offerName, date) {
    var urlencoded = new URLSearchParams()
    urlencoded.append('offerID', id)

    try {
        await fetch(`http://45.145.6.18/database/history/data_json.php`, { method: 'POST', body: urlencoded })
            .then((response) => response.json())
            .then((data) => {
                // console.log(data)
                data.creative = new TextDecoder('utf-8').decode(Uint8Array.from(atob(data.creative), (c) => c.charCodeAt(0)))
                parent.postMessage(data.creative, '*')

                // create div to get formatted text
                let div = document.createElement('div')
                div.innerText = data.creative
                data.creative = div.innerText

                data.header = data.header.replace(/\r\n/g, ',')

                const jsonData = JSON.stringify(data, null, 2)
                const blob = new Blob([jsonData], { type: 'application/json' })
                const url = URL.createObjectURL(blob)

                const a = document.createElement('a')
                a.href = url
                a.download = `${offerID}_${offerName}_${date.substring(11).replaceAll(':', '_')}.json`
                document.body.appendChild(a)
                a.click()

                URL.revokeObjectURL(url)
                // const creative = parent.document.getElementById('creative')
                // creative.innerText = jsonData.creative
            })
            .catch((error) => console.error('Error:', error))
    } catch (error) {
        console.error('Error downloading the offer:', error)
    }
}
