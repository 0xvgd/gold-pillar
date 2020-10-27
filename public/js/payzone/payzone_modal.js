
// Create Element.remove() function if not exist //polyfill for IE
if (!('remove' in Element.prototype)) {
    Element.prototype.remove = function() {
        if (this.parentNode) {
            this.parentNode.removeChild(this);
        }
    };
}
// Call remove() according to your need
function openLoadingModal(size) {
  if(size == 'loading'){
    pzgLoading.style.width = "200px";
    pzgLoading.style.height = "200px";
    pzgLoading.style.margin =  "calc( 50vh - 100px ) calc( 50vw - 100px )";
    pzgLoading.style.display = "block";
    pzgLoadingBG.style.display = "block";
  }
  else {
    pzgLoading.style.width = size + "vw";
    pzgLoading.style.height = size + "vh";
    pzgLoading.style.margin = (100 - size)/2 + "vh " + (100 - size)/2 + "vw";
    pzgLoading.style.display = "block";
    pzgLoadingBG.style.display = "block";
  }
}
function closeLoadingModal() {
  pzgLoading.style.display = "none";
  pzgLoadingBG.style.display = "none";
}

function openPayzoneModal(size) {
  pzgModal.style.width = size + "vw";
  pzgModal.style.height = size + "vh";
  pzgModal.style.margin = (100 - size)/2 + "vh " + (100 - size)/2 + "vw";
  pzgModal.style.display = "block";
  pzgModalBG.style.display = "block";
}
function sizePayzoneModal(size) {
  if(size=='threed'){
    pzgModal.style.width = "370px";
    pzgModal.style.height = "360px";
    pzgModal.style.margin =  "calc( 50vh - 180px ) calc( 50vw - 190px )";
  }
  else if(size=='loading'){
    pzgModal.style.width = "200px";
    pzgModal.style.height = "200px";
    pzgModal.style.margin =  "calc( 50vh - 100px ) calc( 50vw - 100px )";
  }
  else {
    pzgModal.style.width = size + "vw";
    pzgModal.style.height = size + "vh";
    pzgModal.style.margin =  (100 - size)/2 + "vh " + (100 - size)/2 + "vw";
  }
}
function closePayzoneModal() {
  if (iframepage=='cart' || iframepage=='payment'|| iframepage=='results-process' ){
    var cancelAgree=confirm('Warning, if you have not completed the payment closing this window will cancel the payment. \n If you have completed payment or do not want to complete this payment please click OK');
  }
  else if (iframepage=='results-declined'){
    window.location.href ="<?php echo $PayzoneGateway->getURL('cart-page'); ?>" ;
  }
  else if ( iframepage=='results-process'){

  }
  if(cancelAgree || iframepage=='three-response' || iframepage=='results'  || iframepage=='results-process'){
    pzgModal.style.display = "none";
    document.getElementById('payzone-iframe').remove();
    pzgModalBG.style.display = "none";
    window.self.postMessage({'option':'cancel','value':iframepage},siteRoot);
  }
  switch (iframepage) {
    case 'results-process':
    case 'results':
      window.location.href = homePage ;
      break;
    case 'cart':
    case 'payment':
      //window.location.href = cartPage ;
      break;
    default:
      break;
  }
}

function receiveMessageCart(event){
if (event.origin !== siteRoot)
return;
  switch (event.data['option']) {
    case 'modalsize':
      sizePayzoneModal(event.data['value']);
      break;
    case 'cancel':
      //Action to complete if the user cancels to payment (fired from closing down the modal window
      break;
    case 'iframesrc':
      //track the current page for the iframesrc
      iframepage = event.data['value'];
      break;
    case 'threedresponse':
      closePayzoneModal();
      sendToResults(JSON.parse(event.data['value']));
      break;
    default:
      break;
  }
}

      function sendToResults(data){
        var cartForm = document.getElementById('payzone-payment-form');

        //var cartData = new FormData(cartForm); renoved due to IE incompatibility
        var resultsData;
        resultsData=data;
        resultsData["HashDigest"]=document.getElementsByName("HashDigest")[0].value;//cartData.get('HashDigest');
        resultsData["TransactionDateTime"]=document.getElementsByName("TransactionDateTime")[0].value;//cartData.get('TransactionDateTime');
        resultsData["CallbackURL"]=document.getElementsByName("CallbackURL")[0].value;//cartData.get('CallbackURL');
        resultsData["OrderID"]=document.getElementsByName("OrderID")[0].value;//cartData.get('OrderID');
        resultsData["OrderDescription"]=document.getElementsByName("OrderDescription")[0].value;//cartData.get('OrderDescription');
        resultsData["CurrencyCode"]=document.getElementsByName("CurrencyCode")[0].value;//cartData.get('CurrencyCode');
        resultsData["Amount"]=document.getElementsByName("FullAmount")[0].value;//cartData.get('FullAmount');

        resultsData["AmountMinor"]=document.getElementsByName("Amount")[0].value;//cartData.get('Amount');

        var resForm = document.createElement("form");
        resForm.setAttribute("id", "payzone_results");
        resForm.setAttribute("name", "payzone_results");
        resForm.setAttribute("action", resultsData['CallbackURL']);
        resForm.setAttribute("method", "POST");
        resForm.setAttribute("target", "_self");
        pzgModal.appendChild(resForm);
          for( var key in resultsData ) {
            if( resultsData.hasOwnProperty(key) ) {
              input = document.createElement("input");
              input.setAttribute("name", key);
              input.setAttribute("type", "hidden");
              input.setAttribute("value",resultsData[key]);
              resForm.appendChild(input);
            }
          }
          document.getElementById('payzone_results').submit();
          document.getElementById('payzone_results').remove();
      }
