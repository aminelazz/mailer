<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" href="./favicon.ico" type="image/x-icon">
   <link rel="stylesheet" href="./public/stylesheets/bootstrap_mod.css">
   <link rel="stylesheet" href="./public/stylesheets/styles.css">
   <script src="./public/js/auth.js"></script>
   <title>St-Com</title>
</head>

<body class="row w-100 h-100" onbeforeunload="return preventUnload()">
   <form action="public/functions/send_email.php" method="post" class="p-0" id="sendForm" enctype="multipart/form-data">
      <!-- Start Side panel -->
      <div class="col p-0 position-fixed start-0 h-100" style="max-width: 220px; background-color: #8fa1a3;">
         <div class="m-2 ">
            <center>
               <a href="">
                  <img src="./public/assets/logo.jpg" alt="St-com" width="40%" class="rounded-circle">
               </a>
            </center>
         </div>
         <!-- Navigation Buttons -->
         <div class="row m-auto">
            <a href="#Servers" class="btn py-3 px-0 fw-semibold text-white rounded-0 navigation">
               <div class="d-flex flex-row justify-content-between px-4">
                  <div>Servers</div>
                  <img src="./public/assets/servers.svg" alt="">
               </div>
            </a>
            <a href="#Header" class="btn py-3 px-0 fw-semibold text-white rounded-0 navigation">
               <div class="d-flex flex-row justify-content-between px-4">
                  <div>Header</div>
                  <img src="./public/assets/header.svg" alt="">
               </div>
            </a>
            <a href="#Tools" class="btn py-3 px-0 fw-semibold text-white rounded-0 navigation">
               <div class="d-flex flex-row justify-content-between px-4">
                  <div>Tools</div>
                  <img src="./public/assets/tools.svg" alt="">
               </div>
            </a>
            <a href="#Properties" class="btn py-3 px-0 fw-semibold text-white rounded-0 navigation">
               <div class="d-flex flex-row justify-content-between px-4">
                  <div>Properties</div>
                  <img src="./public/assets/properties.svg" alt="">
               </div>
            </a>
            <a href="#Body" class="btn py-3 px-0 fw-semibold text-white rounded-0 navigation">
               <div class="d-flex flex-row justify-content-between px-4">
                  <div>Body</div>
                  <img src="./public/assets/body.svg" alt="">
               </div>
            </a>
            <a href="#Recipients" class="btn py-3 px-0 fw-semibold text-white rounded-0 navigation">
               <div class="d-flex flex-row justify-content-between px-4">
                  <div>Recipients</div>
                  <img src="./public/assets/recepients.svg" alt="">
               </div>
            </a>
            <a href="#Result" class="btn py-3 px-0 fw-semibold text-white rounded-0 navigation">
               <div class="d-flex flex-row justify-content-between px-4">
                  <div>Result</div>
                  <img src="./public/assets/status.svg" alt="">
               </div>
            </a>
            <!-- Start Button -->
            <button id="start" type="submit" class="send btn btn-success bord-0 mt-4 mb-3 mx-auto text-white fw-semibold" style="height: 43px; width: 85%;" onclick="checkFields()">Start</button>
            <!-- Play & Pause & Stop Buttons -->
            <div id="controlArea" class="d-flex justify-content-evenly mx-auto h-auto p-0 invisible" style="width: 85%;">
               <button id="play" type="button" class="col-3 btn btn-play border-2" onclick="startSend()">
                  <img src="./public/assets/play.svg" alt="Play" class=" pb-1">
               </button>
               <button id="pause" type="button" class="col-3 btn btn-warning border-2" onclick="pauseSend()">
                  <img src="./public/assets/pause.svg" alt="Pause" class=" pb-1">
               </button>
               <button id="stop" type="button" class="col-3 btn btn-danger border-2" onclick="stopSend()">
                  <img src="./public/assets/stop.svg" alt="Cancel" class=" pb-1">
               </button>
            </div>
            <!-- Status Label -->
            <div class="text-white text-center fw-semibold my-3" id="status">Status: Pending</div>
            <!-- Progress bar & Send count -->
            <div>
               <div class="progress p-0 mx-auto invisible" id="progressBarContainer" role="progressbar" style="width: 85%;" aria-label="Sent emails" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                  <div class="progress-bar bg-play" id="progressBar" style="width: 0%">0%</div>
               </div>
               <div id="sendCount" class="text-white text-center fw-semibold my-1" style="font-size: 0.85rem;"></div>
            </div>
         </div>
      </div>
      <!-- End Side Panel -->
      <!-- Start Main Menu -->
      <div class="d-flex h-100">
         <div style="width: 250px"></div>
         <div class="col mb-4 container-xxl" style="padding-top: 10px;">
            <div class="d-flex align-items-center justify-content-between">
               <h4>St-Com Mailing</h4>
               <!-- Welcome & Logout -->
               <div class="btn-group">
                  <button type="button" class="btn">Welcome, <span id="mailerName" class="fw-semibold"></span></button>
                  <button type="button" class="btn dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                     <span class="visually-hidden">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu  dropdown-menu-end">
                     <li>
                        <button type="button" class="btn btn-outline-danger dropdown-item" onclick="logout()">Log out</button>
                     </li>
                  </ul>
               </div>

            </div>
            <hr>
            <!-- Tabs -->
            <ul class="nav nav-tabs" id="nav-tab" role="tablist">
               <li class=" nav-item" role="presentation">
                  <button class="nav-link active" id="nav-send-tab" data-bs-toggle="tab" data-bs-target="#nav-send" type="button" role="tab" aria-controls="nav-send" aria-selected="true" onclick="hideRefresh()">Send</button>
               </li>
               <li class="nav-item me-auto" role="presentation">
                  <button class="nav-link" id="nav-history-tab" data-bs-toggle="tab" data-bs-target="#nav-history" type="button" role="tab" aria-controls="nav-history" aria-selected="false" onclick="showRefresh()">History</button>
               </li>
               <div class="me-2 invisible" id="refreshDiv">
                  <button class="my-auto btn btn-primary border-0 d-flex gap-1 align-items-center refresh-dropbox" type="button" onclick="refreshIframe()">
                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-repeat" viewBox="0 0 16 16">
                        <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"></path>
                        <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"></path>
                     </svg>
                     Refresh
                  </button>
               </div>
            </ul>
            <!-- Content -->
            <div class="tab-content pt-2" style="height: 90%;">
               <!-- Send -->
               <div class="tab-pane active" id="nav-send" role="tabpanel" aria-labelledby="nav-send-tab" tabindex="0">
                  <!-- Start Servers -->
                  <div id="Servers" class="card">
                     <h5 class="card-header fw-semibold">
                        Servers Section
                     </h5>
                     <div class="card-body border-3 border-top border-success">
                        <div class="row">
                           <!-- Start Server Field -->
                           <div class="col">
                              <label for="servers" class="form-label fw-semibold">
                                 Servers
                              </label>
                              <textarea spellcheck="false" name="servers" id="servers" class="form-control" pattern="/^(?:[\w.-]+:\d+:(?:tls|ssl):[\w.-]+@[\w.-]+:\S+)$/gm" style="height: 134px; resize: none; font-size: 0.85rem; overflow: auto;" placeholder="Format 1: Host:Port:TLS:User:Pass&#10;&#10;Format 2: Host:Port:SSL:User:Pass" oninput="changeTimeValues()" required></textarea>
                           </div>
                           <!-- End Server Field -->
                           <div class="col">
                              <div class="row pb-3">
                                 <div class="col">
                                    <label for="pauseAfterSend" class="form-label fw-semibold">
                                       Pause After Send <span class="fst-italic">(Seconds)</span>
                                    </label>
                                    <input type="number" name="pauseAfterSend" id="pauseAfterSend" class="form-control" style="height: 43px;" value="5" min="0" onchange="changeTimeValues()">
                                 </div>
                                 <div class=" col">
                                    <label for="rotationAfter" class="form-label fw-semibold">
                                       Rotation After <span class="fst-italic">(Seconds)</span>
                                    </label>
                                    <input type="number" name="rotationAfter" id="rotationAfter" class="form-control" style="height: 43px;" value="1" min="0">
                                 </div>
                              </div>
                              <div class=" row">
                                 <!-- <div class="col">
                                    <label for="emailPerSecond" class="form-label fw-semibold">
                                       Email per second
                                    </label>
                                 <input type="number" name="emailPerSecond" id="emailPerSecond" class="form-control" style="height: 43px;" value="1" min="1" onchange="changeTimeValues()">
                                 </div> -->
                                 <div class="col">
                                    <label for="BCCnumber" class="form-label fw-semibold">
                                       Number of Emails In Bcc
                                    </label>
                                    <input type="number" name="BCCnumber" id="BCCnumber" class="form-control" style="height: 43px;" value="3" min="1" max="10" onchange="changeTimeValues()">
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- End Servers -->

                  <hr class=" my-4">

                  <!-- Start Header -->
                  <div id="Header" class="card">
                     <h5 class="card-header fw-semibold">
                        Header Section
                     </h5>
                     <div class="card-body border-3 border-top border-success">
                        <div class="row">
                           <!-- Custom Header -->
                           <div class="col">
                              <label for="headers" class="form-label fw-semibold">
                                 Custom Header
                              </label>
                              <textarea spellcheck="false" name="headers" id="headers" class="form-control" style="height: 134px; resize: none; font-size: 0.85rem;">Message-ID: <[anl_20]@[domain]>&#10;X-Mailer: St-Com v1.0-Ref[n_2]&#10;Auto-Submitted: auto-generated&#10;X-Auto-Response-Suppress: OOF, AutoReply&#10;X-Abuse: Please report abuse here <mailto:abuse@[domain]?c=[n_10]></textarea>
                           </div>
                           <!-- Message Properties -->
                           <div class="col">
                              <!-- Content Type & Charset -->
                              <div class="row pb-3">
                                 <!-- Content Type -->
                                 <div class="col">
                                    <label for="contentType" class="form-label fw-semibold">
                                       Content Type
                                    </label>
                                    <select name="contentType" id="contentType" class="form-select" style="height: 43px;">
                                       <option value="text/html">
                                          text/html
                                       </option>
                                       <option value="text/plain">
                                          text/plain
                                       </option>
                                       <option value="multipart/alternative">
                                          multipart/alternative
                                       </option>
                                    </select>
                                 </div>
                                 <!-- Charset -->
                                 <div class="col">
                                    <label for="charset" class="form-label fw-semibold">
                                       Charset
                                    </label>
                                    <select name="charset" id="charset" class="form-select" style="height: 43px;">
                                       <option value="UTF-8">
                                          UTF-8
                                       </option>
                                       <option value="US-ASCII">
                                          US-ASCII
                                       </option>
                                       <option value="ISO-8859-1">
                                          ISO-8859-1
                                       </option>
                                    </select>
                                 </div>
                              </div>
                              <!-- Content-Transfer-Encoding & Priority -->
                              <div class="row">
                                 <!-- Content-Transfer-Encoding -->
                                 <div class="col">
                                    <label for="encoding" class="form-label fw-semibold">
                                       Content-Transfer-Encoding
                                    </label>
                                    <select name="encoding" id="encoding" class="form-select" style="height: 43px;">
                                       <option value="7bit">
                                          7bit
                                       </option>
                                       <option value="8bit">
                                          8bit
                                       </option>
                                       <option value="base64">
                                          Base64
                                       </option>
                                       <option value="binary">
                                          Binary
                                       </option>
                                       <option value="quoted-printable">
                                          Quoted-Printable
                                       </option>
                                    </select>
                                 </div>
                                 <!-- Priority -->
                                 <div class="col">
                                    <label for="priority" class="form-label fw-semibold">
                                       Priority
                                    </label>
                                    <select name="priority" id="priority" class="form-select" style="height: 43px;">
                                       <option value="">
                                          Default
                                       </option>
                                       <option value="5">
                                          Low
                                       </option>
                                       <option value="3" selected>
                                          Normal
                                       </option>
                                       <option value="1">
                                          High
                                       </option>
                                    </select>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- End Header -->

                  <hr class="my-4">

                  <!-- Start Tools -->
                  <div id="Tools" class="card">
                     <h5 class="card-header fw-semibold">
                        Tools Section
                     </h5>
                     <div class="card-body border-3 border-top border-success">
                        <div class="row">
                           <!-- Left Side -->
                           <div class="col">
                              <!-- Generator -->
                              <div class="row mx-0">
                                 <div class="col pb-3 p-0">
                                    <label class="form-label fw-semibold" for="length">
                                       Generator Settings
                                    </label>
                                    <div class="row flex-nowrap mx-0">
                                       <div class="col-9">
                                          <div class="row flex-nowrap align-items-center justify-content-around border rounded-2 rounded-end-0 w-auto p-0" style="height: 43px;">
                                             <div class="w-auto px-0">
                                                <div class="form-check form-switch">
                                                   <input class="form-check-input" id="AZ" type="checkbox" role="switch" value="AZ">
                                                   <label class="form-check-label" for="AZ">Range A-Z</label>
                                                </div>
                                             </div>
                                             <div class="w-auto px-0">
                                                <div class="form-check form-switch">
                                                   <input class="form-check-input" id="az" type="checkbox" role="switch" value="az">
                                                   <label class="form-check-label" for="az">Range a-z</label>
                                                </div>
                                             </div>
                                             <div class="w-auto px-0">
                                                <div class="form-check form-switch">
                                                   <input class="form-check-input" id="09" type="checkbox" role="switch" value="09">
                                                   <label class="form-check-label" for="09">Range 0-9</label>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="col-3 p-0">
                                          <input type="number" name="length" id="length" class="form-control rounded-start-0" min="1" style="height: 43px; margin-left: -1px;" placeholder="Length">
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <!-- Result -->
                              <div class="col">
                                 <label class="form-label fw-semibold" for="pattern">
                                    Pattern Value
                                 </label>
                                 <div class="row mx-0">
                                    <div class="col-9 p-0">
                                       <input type="text" class="form-control rounded-end-0" name="pattern" id="pattern" style="height: 43px;" placeholder="Pattern" disabled>
                                    </div>
                                    <div class="col-3 p-0">
                                       <button type="button" class="send btn btn-success w-100 rounded-start-0" id="pBtn" style="height: 43px;" onclick="generatePattern()">
                                          Get Pattern
                                       </button>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <!-- Right Side -->
                           <div class="col">
                              <label class="form-label fw-semibold">
                                 Instruction
                              </label>
                              <div class="border rounded-2 py-2" style="background-color: #E9ECEF;">
                                 <ol>
                                    <li><span class="header">General Tags</span>
                                       <ul>
                                          <li>
                                             <span>[an_N]</span>:&nbsp;
                                             Random String Generator
                                          </li>
                                          <li>
                                             <span>[mail_date]</span>:&nbsp;
                                             Date <span class="fst-italic fw-semibold">(<?php echo date('d/m/Y') ?>)</span>
                                          </li>
                                          <li>
                                             <span>[time]</span>:&nbsp;
                                             Time <span class="fst-italic fw-semibold">(<?php echo date('H:i:s') ?>)</span>
                                          </li>
                                          <li>
                                             <span>[email]</span>:&nbsp;
                                             Reciever Email <span class="fst-italic fw-semibold">(email_user@domain.com)</span>
                                          </li>
                                          <li>
                                             <span>[first_name]</span>:&nbsp;
                                             Reciever Name <span class="fst-italic fw-semibold">(email_user)</span>
                                          </li>
                                          <li>
                                             <span>[domain]</span>:&nbsp;
                                             Server Domain
                                          </li>
                                          <li>
                                             <span>[url]</span>:&nbsp;
                                             Your Link
                                          </li>
                                       </ul>
                                    </li>
                                    <li><span class="header">Example</span>
                                       <ul>
                                          <li>
                                             Hello <span class="fst-italic fw-semibold">[user]</span>
                                             Hello <span class="fst-italic fw-semibold">user</span>
                                          </li>
                                          <li>
                                             Your Code Is <span class="fst-italic fw-semibold">[an_8]</span>
                                             Your Code Is <span class="fst-italic fw-semibold">A4s5FhrN</span>
                                          </li>
                                       </ul>
                                    </li>
                                 </ol>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- End Tools -->

                  <hr class="my-4">

                  <!-- Start Properties -->
                  <div id="Properties" class="card">
                     <h5 class="card-header fw-semibold">
                        Properties Section
                     </h5>
                     <div class="card-body border-3 border-top border-success">
                        <!-- Offer Infos & Country -->
                        <div class="row pb-3">
                           <!-- Offer Infos -->
                           <div class="col-6">
                              <label for="offerInfos" class="form-label fw-semibold">
                                 Offer Infos
                              </label>
                              <div class="input-group" id="offerInfos">
                                 <input type="text" class="form-control w-25" id="offerID" name="offerID" placeholder="Offer ID" style="height: 43px;" required>
                                 <input type="text" class="form-control w-75" id="offerName" name="offerName" placeholder="Offer Name" style="height: 43px;" required>
                              </div>
                           </div>
                           <!-- Country -->
                           <div class="col-6">
                              <label for="country" class="form-label fw-semibold">
                                 Country
                              </label>
                              <select class="form-select" id="country" name="country" aria-placeholder="Choose a country..." placeholder="Choose a country..." style="height: 43px;" required>
                                 <option value="" selected hidden style="color: #595C66;">Choose a country...</option>
                                 <!-- Europe Countries -->
                                 <optgroup label="Europe">
                                    <option value="1">Albania</option>
                                    <option value="2">Andorra</option>
                                    <option value="3">Austria</option>
                                    <option value="4">Belarus</option>
                                    <option value="5">Belgium</option>
                                    <option value="6">Bosnia and Herzegovina</option>
                                    <option value="7">Bulgaria</option>
                                    <option value="8">Croatia</option>
                                    <option value="9">Cyprus</option>
                                    <option value="10">Czech Republic</option>
                                    <option value="11">Denmark</option>
                                    <option value="12">Estonia</option>
                                    <option value="13">Faroe Islands</option>
                                    <option value="14">Finland</option>
                                    <option value="15">France</option>
                                    <option value="16">Germany</option>
                                    <option value="17">Gibraltar</option>
                                    <option value="18">Greece</option>
                                    <option value="19">Hungary</option>
                                    <option value="20">Iceland</option>
                                    <option value="21">Ireland</option>
                                    <option value="22">Isle of Man</option>
                                    <option value="23">Italy</option>
                                    <option value="24">Latvia</option>
                                    <option value="25">Liechtenstein</option>
                                    <option value="26">Lithuania</option>
                                    <option value="27">Luxembourg</option>
                                    <option value="28">Macedonia</option>
                                    <option value="29">Malta</option>
                                    <option value="30">Moldova</option>
                                    <option value="31">Monaco</option>
                                    <option value="32">Montenegro</option>
                                    <option value="33">Netherlands</option>
                                    <option value="34">Norway</option>
                                    <option value="35">Poland</option>
                                    <option value="36">Portugal</option>
                                    <option value="37">Romania</option>
                                    <option value="38">Russia</option>
                                    <option value="39">San Marino</option>
                                    <option value="40">Serbia</option>
                                    <option value="41">Slovakia</option>
                                    <option value="42">Slovenia</option>
                                    <option value="43">Spain</option>
                                    <option value="44">Sweden</option>
                                    <option value="45">Switzerland</option>
                                    <option value="46">Ukraine</option>
                                    <option value="47">United Kingdom</option>
                                 </optgroup>
                                 <!-- America -->
                                 <optgroup label="America">
                                    <option value="48">United States</option>
                                    <option value="49">Canada</option>
                                 </optgroup>
                                 <!-- Australia -->
                                 <optgroup label="Australia">
                                    <option value="50">Australia</option>
                                 </optgroup>
                              </select>
                           </div>
                        </div>
                        <!-- From Name & Subject -->
                        <div class="row pb-3">
                           <!-- From Name -->
                           <div class="col-6">
                              <label for="fromName" class="form-label fw-semibold">
                                 From Name
                              </label>
                              <div class="row mx-0">
                                 <div class="col-4 p-0">
                                    <select class="form-select border-end-0 rounded-end-0" name="fromNameEncoding" id="fromNameEncoding" style="height: 43px;">
                                       <option value="">
                                          Default
                                       </option>
                                       <option value="7bit">
                                          7bit
                                       </option>
                                       <option value="base64" selected>
                                          Base64
                                       </option>
                                       <option value="binary">
                                          Binary
                                       </option>
                                       <option value="quoted-printable">
                                          Quoted-Printable
                                       </option>
                                    </select>
                                 </div>
                                 <div class="col-8 p-0">
                                    <input type="text" class="form-control rounded-start-0" id="fromName" name="fromName" placeholder="From Name" value="[au_10]" style="height: 43px;" required>
                                 </div>
                              </div>
                           </div>
                           <!-- Subject -->
                           <div class="col-6">
                              <label for="subject" class="form-label fw-semibold">
                                 Subject
                              </label>
                              <div class="row mx-0">
                                 <div class="col-4 p-0">
                                    <select class="form-select border-end-0 rounded-end-0" name="subjectEncoding" id="subjectEncoding" style="height: 43px;">
                                       <option value="">
                                          Default
                                       </option>
                                       <option value="7bit">
                                          7bit
                                       </option>
                                       <option value="base64" selected>
                                          Base64
                                       </option>
                                       <option value="binary">
                                          Binary
                                       </option>
                                       <option value="quoted-printable">
                                          Quoted-Printable
                                       </option>
                                    </select>
                                 </div>
                                 <div class="col-8 p-0">
                                    <input type="text" class="form-control rounded-start-0" id="subject" name="subject" placeholder="Subject" value="[au_10]" style="height: 43px;" required>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <!-- From Email & Reply-To & Return Path -->
                        <div class="row">
                           <!-- From Email -->
                           <div class="col-4">
                              <label for="fromEmail" class="form-label fw-semibold">From Email</label>
                              <div class="row mx-0">
                                 <div class="col-4 p-0 border rounded-2 rounded-end-0 d-flex align-items-center justify-content-center">
                                    <div class="form-check form-switch">
                                       <input class="form-check-input" id="fromEmailCheck" name="fromEmailCheck" type="checkbox" role="switch" value="true" checked>
                                       <label class="form-check-label" for="fromEmailCheck">E.A.L</label>
                                    </div>
                                 </div>
                                 <div class="col-8 p-0">
                                    <input type="text" name="fromEmail" id="fromEmail" class="form-control border-start-0 rounded-start-0" style="height: 43px;" value="[anl_20]@email.com">
                                 </div>
                              </div>
                           </div>
                           <!-- Reply-To -->
                           <div class="col-4">
                              <label for="replyTo" class="form-label fw-semibold">Reply-To</label>
                              <div class="row mx-0">
                                 <div class="col-4 p-0 border rounded-2 rounded-end-0 d-flex align-items-center justify-content-center">
                                    <div class="form-check form-switch">
                                       <input class="form-check-input" id="replyToCheck" name="replyToCheck" type="checkbox" role="switch" value="true">
                                       <label class="form-check-label" for="replyToCheck">R.A.L</label>
                                    </div>
                                 </div>
                                 <div class="col-8 p-0">
                                    <input type="text" name="replyTo" id="replyTo" class="form-control border-start-0 rounded-start-0" style="height: 43px;" value="[anl_20]@email.com">
                                 </div>
                              </div>
                           </div>
                           <!-- Return Path -->
                           <div class="col-4">
                              <label for="returnPath" class="form-label fw-semibold">Return Path</label>
                              <div class="row mx-0">
                                 <div class="col-4 p-0 border rounded-2 rounded-end-0 d-flex align-items-center justify-content-center">
                                    <div class="form-check form-switch">
                                       <input class="form-check-input" id="returnPathCheck" name="returnPathCheck" type="checkbox" role="switch" value="true">
                                       <label class="form-check-label" for="returnPathCheck">C.R</label>
                                    </div>
                                 </div>
                                 <div class="col-8 p-0">
                                    <input type="text" name="returnPath" id="returnPath" class="form-control border-start-0 rounded-start-0" style="height: 43px;" value="[anl_20]@email.com">
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- End Properties -->

                  <hr class="my-4">

                  <!-- Start Body -->
                  <div id="Body" class="card">
                     <h5 class="card-header fw-semibold">
                        Body Section
                     </h5>
                     <div class="card-body border-3 border-top border-success">
                        <!-- Link & Attachements -->
                        <div class="row pb-3">
                           <!-- Link -->
                           <div class="col-6">
                              <label for="link" class="form-label fw-semibold">Link</label>
                              <input type="text" class="form-control mx-0" name="link" id="link" style="height: 43px;" placeholder="http://example.com">
                           </div>
                           <!-- Attachements -->
                           <div class="col-6">
                              <label for="attachements" class="form-label fw-semibold">Attachements</label>
                              <div class="row mx-0">
                                 <div class="col-8 p-0">
                                    <!-- Attachements name & Clear files button -->
                                    <div class="row mx-0" style="position: relative;">
                                       <input type="text" name="attachementsName" id="attachementsName" class="form-control rounded-end-0 col" style="height: 43px; font-size: 0.85rem; position: absolute;" placeholder="Your files names" disabled>
                                       <!-- Clear files button -->
                                       <button type="button" id="clearAttachements" class="btn btn-outline-dark rounded-0 p-auto invisible" style="z-index: 1; height: 43px; width: 43px; position: absolute; right: 0; border-color: transparent; padding-top: 2px;" onclick="clearFiles()">
                                          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
                                             <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"></path>
                                          </svg>
                                       </button>
                                    </div>
                                 </div>
                                 <input type="file" name="attachements[]" id="attachements" hidden multiple onchange="fileUpload()" oninput="fileUpload()">
                                 <label class="send btn btn-success col-4 rounded-start-0 d-flex justify-content-center align-items-center" for="attachements" style="height: 43px;">Upload</label>
                              </div>

                           </div>
                        </div>
                        <!-- Creative and Preview -->
                        <div class="row">
                           <!-- Creative -->
                           <div class="col-6">
                              <label for="creative" class="form-label fw-semibold">Creative</label>
                              <textarea spellcheck="false" name="creative" id="creative" class="form-control w-100 language-html" style="height: 391px; font-family: monospace; resize: none;" oninput="previewCreative()" required></textarea>
                           </div>
                           <!-- Preview -->
                           <div class="col-6">
                              <label for="preview" class="form-label fw-semibold">Preview</label>
                              <div name="preview" id="preview" class="form-control w-100" style="height: 391px; overflow: auto;"></div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- End Body -->

                  <hr class="my-4">

                  <!-- Start Recipients -->
                  <div id="Recipients" class="card">
                     <h5 class="card-header fw-semibold">
                        Recipients Section
                     </h5>
                     <div class="card-body border-3 border-top border-success">
                        <!-- Recipients & Blacklist & Failed -->
                        <div class="row">
                           <!-- Recipients -->
                           <div class="col-4">
                              <label for="recipients" class="form-label fw-semibold">Recipients</label>
                              <textarea spellcheck="false" name="recipients" id="recipients" rows="15" class="form-control w-100" style="resize: none;" oninput="changeTimeValues()" required></textarea>
                           </div>
                           <!-- Blacklist -->
                           <div class=" col-4">
                              <label for="blacklist" class="form-label fw-semibold">Blacklist</label>
                              <textarea spellcheck="false" name="blacklist" id="blacklist" rows="15" class="form-control w-100" style="resize: none;"></textarea>
                           </div>
                           <!-- Failed -->
                           <div class="col-4">
                              <label for="failed" class="form-label fw-semibold">Failed</label>
                              <textarea spellcheck="false" name="failed" id="failed" rows="15" class="form-control w-100 bg-white" style="resize: none;" disabled></textarea>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- End Recipients -->

                  <hr class="my-4">

                  <!-- Start Result -->
                  <div id="Result" class="card">
                     <h5 class="card-header fw-semibold">
                        Result Section
                     </h5>
                     <div id="responseTable" class="card-body border-3 border-top border-success" style="height: 377px; overflow: auto;">
                        <table class="table table-striped">
                           <tbody id="responseArea">

                           </tbody>
                        </table>
                     </div>
                  </div>
                  <!-- End Result -->
               </div>
               <!-- History -->
               <div class="tab-pane h-100" id="nav-history" class="pt-2" role="tabpanel" aria-labelledby="nav-history-tab" tabindex="0" style="height: initial;">
                  <iframe id="dropboxFolder" width="100%" height="100%" src="./database/history/history.html" title="Dropbox history folder" frameborder="0" allowfullscreen></iframe>
               </div>
            </div>
         </div>
         <!-- End Main Menu -->
   </form>
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="./public/js/bootstrap.bundle.js"></script>
   <script src="./public/js/uploadToDropbox.js"></script>
   <script src="./public/js/script.js"></script>
</body>

</html>