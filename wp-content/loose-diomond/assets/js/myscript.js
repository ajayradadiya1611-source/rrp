// HEADER MENU JS
$(window).on("scroll", function () {
  var scroll = $(window).scrollTop();

  if (scroll >= 10) {
    $("header").addClass("nav-fixed");
  } else {
    $("header").removeClass("nav-fixed");
  }
});

// tooltip css
$(function () {
  $('[data-toggle="tooltip"]').tooltip();
});

// inventory scrollbar js

$(document).ready(function () {
  $(".more").click(function () {
    $(".more-shape").removeClass("d-none");
    $(".more").addClass("d-none");
  });

  $(function () {
    var $propertiesForm = $(".mall-category-filter");
    var $body = $("body");

    $body.on("click", ".js-mall-filter", function () {
      var $input = $(this).find("input");
      $(this).toggleClass("mall-filter__option--selected");
      $input.prop("checked", !$input.prop("checked"));
      $propertiesForm.trigger("submit");
    });
    $body.on("click", ".js-mall-clear-filter", function () {
      var $parent = $(this).closest(".mall-property");

      $parent.find(':input:not([type="checkbox"])').val("");
      $parent.find('input[type="checkbox"]').prop("checked", false);
      $parent
        .find(".mall-filter__option--selected")
        .removeClass("mall-filter__option--selected");

      var slider = $parent.find(".price-slider-handles")[0];
      if (slider) {
        slider.noUiSlider.updateOptions({
          start: [slider.dataset.min, slider.dataset.max],
        });
      }
      var carat_slider = $parent.find(".carat-slider-handles")[0];
      if (carat_slider) {
        carat_slider.noUiSlider.updateOptions({
          start: [carat_slider.dataset.min, carat_slider.dataset.max],
        });
      }
      var table_slider = $parent.find(".table-slider-handles")[0];
      if (table_slider) {
        table_slider.noUiSlider.updateOptions({
          start: [table_slider.dataset.min, table_slider.dataset.max],
        });
      }
      var depth_slider = $parent.find(".depth-slider-handles")[0];
      if (depth_slider) {
        depth_slider.noUiSlider.updateOptions({
          start: [depth_slider.dataset.min, depth_slider.dataset.max],
        });
      }
      var lwratio_slider = $parent.find(".lwratio-slider-handles")[0];
      if (lwratio_slider) {
        lwratio_slider.noUiSlider.updateOptions({
          start: [lwratio_slider.dataset.min, lwratio_slider.dataset.max],
        });
      }
      var cut_slider = $parent.find(".cut-slider-handles")[0];
      if (cut_slider) {
        cut_slider.noUiSlider.updateOptions({
          start: [cut_slider.dataset.min, cut_slider.dataset.max],
        });
      }
      var color_slider = $parent.find(".color-slider-handles")[0];
      if (color_slider) {
        color_slider.noUiSlider.updateOptions({
          start: [cut_slider.dataset.min, cut_slider.dataset.max],
        });
      }
      var clarity_slider = $parent.find(".clarity-slider-handles")[0];
      if (clarity_slider) {
        clarity_slider.noUiSlider.updateOptions({
          start: [clarity_slider.dataset.min, clarity_slider.dataset.max],
        });
      }
      $propertiesForm.trigger("submit");
    });

    $propertiesForm.on("submit", function (e) {
      e.preventDefault();

      $.publish("mall.category.filter.start");
      $(this).request("categoryFilter::onSetFilter", {
        loading: $.oc.stripeLoadIndicator,
        complete: function (response) {
          $.oc.stripeLoadIndicator.hide();
          if (response.responseJSON.hasOwnProperty("queryString")) {
            history.replaceState(
              null,
              "",
              "?" + response.responseJSON.queryString
            );
          }
        },
        error: function () {
          $.oc.stripeLoadIndicator.hide();
          $.oc.flashMsg({
            text: "Fehler beim Ausführen der Suche.",
            class: "error",
          });
          $.publish("mall.category.filter.error");
        },
      });
    });
    $(".price-slider-handles").each(function () {
      var el = this;
      noUiSlider
        .create(el, {
          start: [el.dataset.start, el.dataset.end],
          step: 1,
          behaviour: "drag-tap",
          connect: true,
          range: {
            min: [parseInt(el.dataset.min)],
            "50%": [6000, 1],
            max: [parseInt(el.dataset.max)],
          },
          pips: {
            mode: "range",
            density: 100,
          },
        })
        .on("change", function (values) {
          let price_min = Math.floor(values[0]);
          let price_max = Math.floor(values[1]);
          let price = price_min.toString() + "," + price_max.toString();
          $("#min-price").val(price_min + ".00");
          $("#max-price").val(price_max + ".00");
          let address = updateQueryStringParameter("price", price); // ADD PARAMETER TO URL OR UDPATE VALUE
        });
    });
    // Event listeners to update the slider when input values change
    $("#min-price").on("change", function () {
      var minVal = parseFloat($(this).val());
      var maxVal = parseFloat($("#max-price").val());
      $(".price-slider-handles")[0].noUiSlider.set([minVal, maxVal]);
    });

    $("#max-price").on("change", function () {
      var minVal = parseFloat($("#min-price").val());
      var maxVal = parseFloat($(this).val());
      $(".price-slider-handles")[0].noUiSlider.set([minVal, maxVal]);
    });

    $(".carat-slider-handles").each(function () {
      var el = this;
      noUiSlider
        .create(el, {
          start: [el.dataset.start, el.dataset.end],
          step: 0.01,
          connect: true,
          range: {
            min: [parseFloat(el.dataset.min)],
            max: [parseFloat(el.dataset.max)],
          },
        })
        .on("change", function (values) {
          var carat_min = values[0];
          var carat_max = values[1];
          var carat = carat_min.toString() + "," + carat_max.toString();
          $("#min-carat").val(carat_min);
          $("#max-carat").val(carat_max);
          var address = updateQueryStringParameter("carat", carat); // ADD PARAMETER TO URL OR UDPATE VALUE
        });
    });
    // Event listeners to update the slider when input values change
    $("#min-carat").on("change", function () {
      var minVal = parseFloat($(this).val());
      var maxVal = parseFloat($("#max-carat").val());
      $(".carat-slider-handles")[0].noUiSlider.set([minVal, maxVal]);
    });

    $("#max-carat").on("change", function () {
      var minVal = parseFloat($("#min-carat").val());
      var maxVal = parseFloat($(this).val());
      $(".carat-slider-handles")[0].noUiSlider.set([minVal, maxVal]);
    });

    $(".table-slider-handles").each(function () {
      var el = this;
      noUiSlider
        .create(el, {
          start: [el.dataset.start, el.dataset.end],
          step: 0.01,
          connect: true,
          range: {
            min: [parseFloat(el.dataset.min)],
            max: [parseFloat(el.dataset.max)],
          },
        })
        .on("change", function (values) {
          var table_min = values[0];
          var table_max = values[1];
          var table = table_min.toString() + "," + table_max.toString();
          $("#min-table").val(table_min);
          $("#max-table").val(table_max);
          $(".table-number").text(table_min + "%-" + table_max + "%");
          var address = updateQueryStringParameter("table", table); // ADD PARAMETER TO URL OR UDPATE VALUE
        });
    });
    // Event listeners to update the slider when input values change
    $("#min-table").on("change", function () {
      if ($(this).val() < 50.0 || $(this).val() > 80.0) {
        $(this).val(parseFloat(50.0));
      }
      $(".table-number").text(
        $(this).val() + "%-" + $("#max-table").val() + "%"
      );
      $(".table-slider-handles")[0].noUiSlider.set([
        parseFloat($(this).val()),
        parseFloat($("#max-table").val()),
      ]);
    });

    $("#max-table").on("change", function () {
      if ($(this).val() < 50.0 || $(this).val() > 80.0) {
        $(this).val(parseFloat(80.0));
      }
      $(".table-number").text(
        $("#min-table").val() + "%-" + $(this).val() + "%"
      );
      $(".table-slider-handles")[0].noUiSlider.set([
        parseFloat($("#min-table").val()),
        parseFloat($(this).val()),
      ]);
    });

    $(".depth-slider-handles").each(function () {
      var el = this;
      noUiSlider
        .create(el, {
          start: [el.dataset.start, el.dataset.end],
          step: 0.01,
          connect: true,
          range: {
            min: [parseFloat(el.dataset.min)],
            max: [parseFloat(el.dataset.max)],
          },
        })
        .on("change", function (values) {
          var depth_min = values[0];
          var depth_max = values[1];
          var depth = depth_min.toString() + "," + depth_max.toString();
          $("#min-depth").val(depth_min + " %");
          $("#max-depth").val(depth_max + " %");
          $(".depth-number").text(depth_min + "%-" + depth_max + "%");
          var address = updateQueryStringParameter("depth", depth); // ADD PARAMETER TO URL OR UDPATE VALUE
        });
    });
    // Event listeners to update the slider when input values change
    $("#min-depth").on("change", function () {
      $(".depth-number").text($(this).val() + "-" + $("#max-depth").val());
      $(".depth-slider-handles")[0].noUiSlider.set([
        parseFloat($(this).val()),
        parseFloat($("#max-depth").val()),
      ]);
    });

    $("#max-depth").on("change", function () {
      $(".depth-number").text($("#min-depth").val() + "-" + $(this).val());
      $(".depth-slider-handles")[0].noUiSlider.set([
        parseFloat($("#min-depth").val()),
        parseFloat($(this).val()),
      ]);
    });

    $(".lwratio-slider-handles").each(function () {
      var el = this;
      noUiSlider
        .create(el, {
          start: [el.dataset.start, el.dataset.end],
          step: 0.01,
          connect: true,
          range: {
            min: [parseFloat(el.dataset.min)],
            max: [parseFloat(el.dataset.max)],
          },
        })
        .on("change", function (values) {
          var lwratio_min = values[0];
          var lwratio_max = values[1];
          var lwratio = lwratio_min.toString() + "," + lwratio_max.toString();
          $("#min-lwratio").val(lwratio_min);
          $("#max-lwratio").val(lwratio_max);
          $(".lwratio-number").text(lwratio_min + "-" + lwratio_max);
          var address = updateQueryStringParameter("lwratio", lwratio); // ADD PARAMETER TO URL OR UDPATE VALUE
        });
    });
    // Event listeners to update the slider when input values change
    $("#min-lwratio").on("change", function () {
      $(".lwratio-number").text($(this).val() + "-" + $("#max-lwratio").val());
      $(".lwratio-slider-handles")[0].noUiSlider.set([
        parseFloat($(this).val()),
        parseFloat($("#max-lwratio").val()),
      ]);
    });

    $("#max-lwratio").on("change", function () {
      $(".lwratio-number").text($("#min-lwratio").val() + "-" + $(this).val());
      $(".lwratio-slider-handles")[0].noUiSlider.set([
        parseFloat($("#min-lwratio").val()),
        parseFloat($(this).val()),
      ]);
    });

    var cut_array_label = ["Good", "Very Good", "Excellent", "Ideal"];

    var cutarrayformat = {
      to: function (value) {
        return cut_array_label[Math.round(value)];
      },
      from: function (value) {
        return cut_array_label.indexOf(Number(value));
      },
    };
    $(".cut-slider-handles").each(function () {
      var el = this;
      noUiSlider
        .create(el, {
          start: [el.dataset.start, el.dataset.end],
          step: 1,
          connect: true,
          tooltips: false,
          range: {
            min: 0,
            max: cut_array_label.length,
          },
          pips: {
            mode: "steps",
            density: 100,
            format: cutarrayformat,
          },
        })
        .on("change", function (values) {
          var cut_min = Math.floor(values[0]);
          var cut_max = Math.floor(values[1]);
          var cut = cut_min.toString() + "," + cut_max.toString();
          console.log(cut, "cut");
          var address = updateQueryStringParameter("cut", cut); // ADD PARAMETER TO URL OR UDPATE VALUE
        });
    });

    var colorarrayValuesForSlider = [
      "M",
      "L",
      "K",
      "J",
      "I",
      "H",
      "G",
      "F",
      "E",
      "D",
    ];
    var colorarrayformat = {
      to: function (value) {
        return colorarrayValuesForSlider[Math.round(value)];
      },
      from: function (value) {
        return colorarrayValuesForSlider.indexOf(Number(value));
      },
    };
    $(".color-slider-handles").each(function () {
      var el = this;
      noUiSlider
        .create(el, {
          start: [el.dataset.start, el.dataset.end],
          snap: true,
          connect: true,
          tooltips: false,
          step: 1,
          range: {
            min: 0,
            "10%": 1,
            "20%": 2,
            "30%": 3,
            "40%": 4,
            "50%": 5,
            "60%": 6,
            "70%": 7,
            "80%": 8,
            "90%": 9,
            max: 10,
          },
          pips: {
            mode: "steps",
            density: 100,
            format: colorarrayformat,
          },
        })
        .on("change", function (values) {
          var color_min = Math.floor(values[0]);
          var color_max = Math.floor(values[1]);
          var color = color_min.toString() + "," + color_max.toString();
          console.log(color, "color");
          var address = updateQueryStringParameter("color", color); // ADD PARAMETER TO URL OR UDPATE VALUE
        });
    });
    var clarityarrayValuesForSlider = [
      "I2",
      "I1",
      "S13",
      "S12",
      "S11",
      "VS2",
      "VS1",
      "VVS2",
      "VVS1",
      "IF",
    ];
    var clarityarrayformat = {
      to: function (value) {
        return clarityarrayValuesForSlider[Math.round(value)];
      },
      from: function (value) {
        return clarityarrayValuesForSlider.indexOf(Number(value));
      },
    };
    $(".clarity-slider-handles").each(function () {
      var el = this;
      noUiSlider
        .create(el, {
          start: [el.dataset.start, el.dataset.end],
          snap: true,
          connect: true,
          tooltips: false,
          step: 1,
          range: {
            min: 0,
            "10%": 1,
            "20%": 2,
            "30%": 3,
            "40%": 4,
            "50%": 5,
            "60%": 6,
            "70%": 7,
            "80%": 8,
            "90%": 9,
            max: 10,
          },
          pips: {
            mode: "steps",
            density: 100,
            format: clarityarrayformat,
          },
        })
        .on("change", function (values) {
          var clarity_min = Math.floor(values[0]);
          var clarity_max = Math.floor(values[1]);
          var clarity = clarity_min.toString() + "," + clarity_max.toString();
          console.log(clarity, "clarity");
          var address = updateQueryStringParameter("clarity", clarity); // ADD PARAMETER TO URL OR UDPATE VALUE
        });
    });
  });

  // filters script

  $(".checkbox").change(function () {
    var countchecked = $("input:checkbox:checked").length;
    if ($(this).attr("checked")) {
      $(this).removeAttr("checked", false);
    } else {
      $(this).attr("checked", true);
    }
    $(".number").html(countchecked);
  });

  /**
   *   shape-box
   */
  $(document).on("change", ".shape-box", function () {
    var val = "";
    var i = 0;
    var vr = 0;
    $(".shape-box").each(function (i) {
      if ($(this).children("input").attr("checked")) {
        if (vr === 0) {
          vr = 1;
          val += $(this).children("input").val();
        } else {
          val += "," + $(this).children("input").val();
        }
        i++;
      }
    });
    console.log(val);
    var address = updateQueryStringParameter("shape", val); // ADD PARAMETER TO URL OR UDPATE VALUE
  });

  // ALL FITERS MANAGE FUNCTION
  function updateQueryStringParameter(key, value, url = 0) {
    //IF URl PARAMETERE NOT PASSED
    if (url == 0) {
      var url = window.location.href;
    }

    //console.log(url);
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    //console.log("re - "+re);
    var separator = url.indexOf("?") !== -1 ? "&" : "?";

    if (url.match(re)) {
      response = url.replace(re, "$1" + key + "=" + value + "$2");
      //console.log("01");
    } else {
      response = url + separator + key + "=" + value;
      //console.log("02");
    }
    //console.log("URL 1 = " + response);
    //window.location.href = response;
    //window.history.replaceState(null, null, key + "=" + value);

    //REMOVE EMPTY PARAMTER TO URL
    response = response.replace(/&?[^&?]+=(?=(?:&|$))/g, "");
    //console.log("URL 2 = " + response);

    //IF NO PARAMTER PASS REMOVE ?
    if (window.location.href.indexOf("=") > -1) {
      // console.log("==");
      //response = response.replace('?', '');
    }
    //ADD PARAMTER TO URL
    history.pushState({}, "", response);

    var urlArr = response.split("?")[1];
    var linkurl = "https://www.loosegrowndiamond.com/inventory?" + urlArr;
    console.log(linkurl, "linkurl");
    return response;
  }
});

$(document).ready(function () {
  const checkboxGroups = document.querySelectorAll(".count_checkbox");

  checkboxGroups.forEach((group) => {
    const checkboxes = group.querySelectorAll('input[type="checkbox"]');
    const count = group.querySelector(".count");

    checkboxes.forEach(function (checkbox) {
      checkbox.addEventListener("change", function () {
        const countCheckedCheckboxes = Array.from(checkboxes).filter(function (
          checkbox
        ) {
          return checkbox.checked;
        }).length;

        if (countCheckedCheckboxes > 0) {
          count.textContent = ` (${countCheckedCheckboxes})`;
        } else {
          count.textContent = ""; // Hide the count when it's zero.
        }
      });
    });
  });
});


// Search on the mobile 
  const searchIcon = document.getElementById("mobileSearchIcon");
  const searchBox = document.getElementById("mobileSearchBox");
  const searchBtn = document.getElementById("mobileSearchBtn");

  // Toggle search box on icon click
searchIcon.addEventListener("click", function () {
  if (searchBox.style.display === "flex") {
    searchBox.style.display = "none";
  } else {
    searchBox.style.display = "flex";
  }
});

  // Hide search box after 5 seconds when search button clicked
  searchBtn.addEventListener("click", function () {
    setTimeout(function () {
      searchBox.style.display = "none";
    }, 5000);
  });


