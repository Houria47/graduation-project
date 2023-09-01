let SelectBoxItOptions = {
  // Uses the jQueryUI theme for the drop down
  theme: "jqueryui",
};
$("select").selectBoxIt(SelectBoxItOptions);

$(".js-rests-slider").slick({
  slidesToScroll: 1,
  autoplay: true,
});

let Rests = [];
function getRestELement(id) {
  let index = Rests.findIndex((item) => item.id == id);
  return Rests[index];
}
// on rests list load
$(document).ready(function () {
  // get data from Database
  $.ajax({
    method: "GET",
    url: "./../ajax_requests/getRestaurantsAccounts.php",
    data: {},
    success: (res) => {
      console.log(res);
      res = JSON.parse(res);
      console.log(res);
      if (res.status) {
        Rests = res.result.restaurants;
      }
    },
  });
});

// Start filter script
// initial filter data
let rate_Filter = -1,
  state_Filter = -1,
  delivary_Filter = 0,
  reserv_Filter = 0,
  fastfood_Filter = 1,
  searchText_Filter = "";

// get filter data on user input
$(document).on("click", "#rate-filter span", function () {
  $(this).siblings(".selected").removeClass("selected");
  $(this).addClass("selected");
  rate_Filter = $(this).attr("data-val");
});
$(document).on("change", "#stateFilter", function () {
  state_Filter = this.value;
});
$(document).on("change", "#delivary", function () {
  fastfood_Filter = !this.checked;
});
$(document).on("change", "#reserv", function () {
  reserv_Filter = this.checked;
});
$(document).on("change", "#delivary", function () {
  delivary_Filter = this.checked;
});
$(document).on("change", "#searchRestInput", function () {
  searchText_Filter = this.value.trim();
});
// filter rests on searchRecipeForm submit & searchRestBtn click
$(document).on("submit", "#searchRestForm", function (e) {
  e.preventDefault();
  searchText_Filter = this.searchText.value.trim();
  filterRests();
});
$(document).on("click", "#searchRestBtn", filterRests);

function filterRests() {
  let rests = $("#rests-list > li");
  rests.removeClass("d-none");
  let searchArray = searchText_Filter.split(/\s+/);

  let cnt = 0;
  rests.each(function (index, postItem) {
    let restID = $(this).data("restid");
    rest = getRestELement(restID);

    let searchFlag = searchArray.some((el) => rest.name.includes(el));
    let reservFlag = reserv_Filter && !rest.reserv_service;
    let delivaryFlag = delivary_Filter && !rest.delivery_service;
    let fastfoodFlag = !fastfood_Filter && rest.fast_food;
    let rateFlag = true;
    let stateFlag = true;
    if (rate_Filter != -1) {
      if (Math.trunc(rest.rate) != rate_Filter) {
        rateFlag = false;
      }
    }
    if (state_Filter != -1) {
      let states = rest.addresses;
      let stateMatches = states.filter(
        (address) => address.state_id == state_Filter
      );
      if (stateMatches.length == 0) {
        stateFlag = false;
      }
    }

    if (
      searchFlag &&
      !reservFlag &&
      !delivaryFlag &&
      !fastfoodFlag &&
      rateFlag &&
      stateFlag
    ) {
      $(this).fadeIn(300);
      cnt++;
    } else {
      $(this).fadeOut(0);
    }
  });
  if (!cnt) {
    $("#no-result").removeClass("d-none");
    $("#pagination").addClass("d-none");
  } else {
    $("#no-result").addClass("d-none");
    $("#pagination").removeClass("d-none");
  }
}
// End filter script
