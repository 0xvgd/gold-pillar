import "jquery-mask-plugin";
import { ajaxSubmit } from "../../utils/forms";
import { openPayzoneModal } from "../../utils/payzonemodal";
import { createInput } from "../../utils/payzonemodal";
import { sizePayzoneModal } from "../../utils/payzonemodal";
import { closePayzoneModal } from "../../utils/payzonemodal";
import { closeLoadingModal } from "../../utils/payzonemodal";
import { receiveMessageCart } from "../../utils/payzonemodal";
import { sendToResults } from "../../utils/payzonemodal";
import "@oroinc/jquery-creditcardvalidator";

import Pace from "../../utils/pace.min.js";

var siteRoot = GpApp.baseUrl;
var pzgModal = document.getElementById("payzone-payment-modal");
var pzgLoading = document.getElementById("payzone-loading-modal");
var pzgModalBG = document.getElementById("payzone-payment-modal-background");
var pzgLoadingBG = document.getElementById("payzone-loading-modal-background");

$("#new_investment_orderDetail_cardNumber").validateCreditCard(function(
  result
) {
  console.log(
    "Card type: " +
      (result.card_type == null ? "-" : result.card_type.name) +
      "<br>Valid: " +
      result.valid +
      "<br>Length valid: " +
      result.length_valid +
      "<br>Luhn valid: " +
      result.luhn_valid
  );
});

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

$("#add-investment-form").on("submit", function(e) {
  debugger;
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
    $("#investment-modal").modal("toggle");
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

$(".money").mask("##0.00", { reverse: true });

const $range = $("#invest-range");
const $amount = $("#new_investment_amount");
const $totalValue = $(".totalvalue");
const $assetEntryFee = $("#assetEntryFee");
$range.on("input", () => {
  var rangeVal = $range.val() * 1;
  var assetEntryFee = $assetEntryFee.val() * 1;
  $amount.val(rangeVal);
  var totalValue = (rangeVal + rangeVal * assetEntryFee).toFixed(2);
  $totalValue.toArray().forEach((element) => {
    $(element).text(totalValue);
  });
});

$amount.on("keyup", () => {
  var amountVal = $amount.val() * 1;
  var assetEntryFee = $assetEntryFee.val() * 1;
  $range.val(amountVal);
  var totalValue = (amountVal + amountVal * assetEntryFee).toFixed(2);
  $totalValue.toArray().forEach((element) => {
    $(element).text(totalValue);
  });
});
$("#new_investment_orderDetail_cardNumber").mask("0000 0000 0000 0000");
$("#new_investment_orderDetail_cardNumber").on("change", () => {
$("#new_investment_orderDetail_cardNumber").validateCreditCard(
  function(e) {
    return (
      $(this).removeClass(),
      $(this).addClass('form-control card_number '),
      null == e.card_type
        ? void $(".vertical.maestro")
            .slideUp({
              duration: 200,
            })
            .animate(
              {
                opacity: 0,
              },
              {
                queue: !1,
                duration: 200,
              }
            )
        : ($(this).addClass(e.card_type.name),
          "maestro" === e.card_type.name
            ? $(".vertical.maestro")
                .slideDown({
                  duration: 200,
                })
                .animate(
                  {
                    opacity: 1,
                  },
                  {
                    queue: !1,
                  }
                )
            : $(".vertical.maestro")
                .slideUp({
                  duration: 200,
                })
                .animate(
                  {
                    opacity: 0,
                  },
                  {
                    queue: !1,
                    duration: 200,
                  }
                ),
          e.valid ? $(this).addClass("valid") : $(this).removeClass("valid"))
    );
  }
);
});

setTotalValue();


function setTotalValue() {
  var $range = $("#invest-range");
  var $amount = $("#new_investment_amount");
  var $totalValue = $(".totalvalue");
  var $assetEntryFee = $("#assetEntryFee");
  var rangeVal = $range.val() * 1;
  var assetEntryFee = $assetEntryFee.val() * 1;
  $amount.val(rangeVal);
  var totalValue = (rangeVal + rangeVal * assetEntryFee).toFixed(2);
  $totalValue.toArray().forEach((element) => {
    debugger;
    $(element).text(totalValue);
  });
}