import "jodit/build/jodit.min.css";
import "jquery-form/dist/jquery.form.min.js";
import "bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js";
import "bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css";
import "toastr/build/toastr.min.css";
import { ajaxSubmit } from "../../utils/forms";
import { openPayzoneModal } from "../../utils/payzonemodal";
import { createInput } from "../../utils/payzonemodal";
import { sizePayzoneModal } from "../../utils/payzonemodal";
import { closePayzoneModal } from "../../utils/payzonemodal";
import { closeLoadingModal } from "../../utils/payzonemodal";
import { receiveMessageCart } from "../../utils/payzonemodal";
import { sendToResults } from "../../utils/payzonemodal";

import Pace from '../../utils/pace.min.js'

var siteRoot = GpApp.baseUrl;
var pzgModal = document.getElementById("payzone-payment-modal");
var pzgLoading = document.getElementById("payzone-loading-modal");
var pzgModalBG = document.getElementById("payzone-payment-modal-background");
var pzgLoadingBG = document.getElementById("payzone-loading-modal-background");

document
  .getElementById("payzone-payment-modal-background")
  .addEventListener("click", closePayzoneModal);
document
  .getElementById("payzone-modal-close")
  .addEventListener("click", closePayzoneModal);

document
  .getElementById("payzone-payment-modal-background")
  .addEventListener("click", closePayzoneModal);
document
  .getElementById("payzone-modal-close")
  .addEventListener("click", closePayzoneModal);

$("#add-reserve-form").on("submit", function(e) {
  Pace.restart();
  window.addEventListener("message", receiveMessageCart, false);
  $(this).ajaxSubmit({
    success: showResponse,
  });

  e.preventDefault();

  return false;
});

function showResponse(responseText, statusText, xhr, $form) {
  Pace.stop();
  debugger;
  var responseObj = JSON.parse(responseText);
  if (responseObj["StatusCode"] == 3) {
    document.querySelector('input[data-field="hashDigest"]').value =
      responseObj.transactionDetails.hashDigest;
    document.querySelector('input[data-field="transactionDateTime"]').value =
      responseObj.transactionDetails.transactionDateTime;
    document.querySelector('input[data-field="callbackURL"]').value =
      responseObj.transactionDetails.callbackURL;
    document.querySelector('input[data-field="orderID"]').value =
      responseObj.transactionDetails.orderID;
    document.querySelector('input[data-field="orderDescription"]').value =
      responseObj.transactionDetails.orderDescription;
    document.querySelector('input[data-field="currencyCode"]').value =
      responseObj.transactionDetails.currencyCode;
    document.querySelector('input[data-field="fullAmount"]').value =
      responseObj.transactionDetails.fullAmount;
    document.querySelector('input[data-field="amount"]').value =
      responseObj.transactionDetails.amount;

    openPayzoneModal(1);
    var ifrm = document.createElement("iframe");
    ifrm.setAttribute("id", "payzone-iframe");
    ifrm.setAttribute("name", "payzone-iframe");
    debugger;
    ifrm.setAttribute("src", `${GpApp.siteRoot}/images/loading2.gif`);
    ifrm.setAttribute("scrolling", "none");
    ifrm.setAttribute("frameborder", "none");
    var threeForm = document.createElement("form");
    threeForm.setAttribute("id", "payzone_acs");
    threeForm.setAttribute("name", "payzone_acs");
    threeForm.setAttribute("action", responseObj["ACSURL"]);
    threeForm.setAttribute("method", "POST");
    threeForm.setAttribute("target", "payzone-iframe");
    var MD = createInput("MD", responseObj["CrossReference"]);
    var PaREQ = createInput("PaReq", responseObj["PaREQ"]);
    var TermUrl = createInput("TermUrl", responseObj["TermUrl"]);
    pzgModal.appendChild(ifrm);
    pzgModal.appendChild(threeForm);
    threeForm.appendChild(MD);
    threeForm.appendChild(PaREQ);
    threeForm.appendChild(TermUrl);
    openPayzoneModal(5);
    sizePayzoneModal("threed");
    document.getElementById("payzone_acs").submit();
    closeLoadingModal();
  } else {
    closeLoadingModal();
    document.querySelector('input[data-field="hashDigest"]').value =
      responseObj.transactionDetails.hashDigest;
    document.querySelector('input[data-field="transactionDateTime"]').value =
      responseObj.transactionDetails.transactionDateTime;
    document.querySelector('input[data-field="callbackURL"]').value =
      responseObj.transactionDetails.callbackURL;
    document.querySelector('input[data-field="orderID"]').value =
      responseObj.transactionDetails.orderID;
    document.querySelector('input[data-field="orderDescription"]').value =
      responseObj.transactionDetails.orderDescription;
    document.querySelector('input[data-field="currencyCode"]').value =
      responseObj.transactionDetails.currencyCode;
    document.querySelector('input[data-field="fullAmount"]').value =
      responseObj.transactionDetails.fullAmount;
    document.querySelector('input[data-field="amount"]').value =
      responseObj.transactionDetails.amount;
    sendToResults(responseObj);
  }
}
