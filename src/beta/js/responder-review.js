
var site_url = 'http://www.alexsol.me';

// $(document).ready(function () {
  $(function () {
    $('[data-toggle="tooltip"]').tooltip();
  });

  //Fix height issue
  if ($(".user-content").height() < $(".user-nav").height()) {
    $(".user-content").height($(".user-nav").height());
  }

  if ($("#divDeleteCompany").length) {
    $("#divDeleteCompany").hide();
  }

  /*********************************************/
  function removeTags(str) {
    if (str === null || str === "") return false;
    else str = str.toString();

    // Regular expression to identify HTML tags in
    // the input string. Replacing the identified
    // HTML tag with a null string.
    return str.replace(/(<([^>]+)>)/gi, "");
  }

  //Improve question tabel usability by adding pagination.
  if ($(".question-table-data").length > 0) {
    let categories = JSON.parse($("#available_categories").val());
    let q_count = 0;
    let pageNum = $("#pageNum").val();
    //alert(pageNum);
    $(".question-table-data tr").each(function (index) {
      /*
            if(categories[0]['category_id'] != $(this).data('category_id')){
                $(this).css('display', 'none');
            }*/
      if (categories[pageNum]["category_id"] != $(this).data("category_id")) {
        $(this).css("display", "none");
      } else {
        q_count++;
        $(this).find(".question-number").text(q_count);

        //Check follow-up question and show
        let follow_up_type;
        if ($(this).data("question_type") == "yes-no") {
          if ($(this).find("input[type=radio]:checked").hasClass("yes-check"))
            follow_up_type = "yes";
          else if (
            $(this).find("input[type=radio]:checked").hasClass("no-check")
          )
            follow_up_type = "no";
        } else if ($(this).data("question_type") == "mcq") {
          if ($(this).find("input[type=radio]:checked").data("tip") == "yes")
            follow_up_type = "yes";
          if ($(this).find("input[type=radio]:checked").data("tip") == "no")
            follow_up_type = "no";
        }

        if (follow_up_type == "yes") {
          let follow_up_id = parseInt($(this).data("question_yes_follow_up"));
          if (follow_up_id) {
            let _this = $(this);
            $(this)
              .parents("table")
              .find(".follow-up")
              .each(function () {
                let q_id = parseInt($(this).attr("id").substring(9));
                if (q_id == follow_up_id) {
                  let follow_up = $(this).clone();
                  _this.after(follow_up);
                  follow_up.find(".question-number").text("-");
                  follow_up.fadeIn();
                  $(this).remove();
                }
              });
          }
        } else if (follow_up_type == "no") {
          let follow_up_id = parseInt($(this).data("question_no_follow_up"));
          if (follow_up_id) {
            let _this = $(this);
            $(this)
              .parents("table")
              .find(".follow-up")
              .each(function () {
                let q_id = parseInt($(this).attr("id").substring(9));
                if (q_id == follow_up_id) {
                  let follow_up = $(this).clone();
                  _this.after(follow_up);
                  follow_up.find(".question-number").text("-");
                  follow_up.fadeIn();
                  $(this).remove();
                }
              });
          }
        }
      }
    });

    $("#totalPages").html(categories.length);

    if (pageNum == 0) {
      let page_number = $(".table-page-number");
      page_number.html(
        categories[0]["category_name"] + '   <i class="fas fa-info-circle"></i>'
      );
      page_number.data("category_pos", 0);
      $(".table-page-next").data("category_pos", 1);
      $(".table-page-prev").prop("disabled", true);
      $("#pageNumber").html(parseInt(pageNum) + 1);
      let category_text_custom = $("#category_text_custom");
      category_text_custom.text(removeTags(categories[0]["category_details"]));
    } else {
      let page_number = $(".table-page-number");
      page_number.text(categories[pageNum]["category_name"]);
      page_number.data("category_pos", pageNum);

      $(".table-page-next").data("category_pos", pageNum + 1);
      $(".table-page-prev").data("category_pos", pageNum - 1);
      $(".table-page-prev").prop("disabled", false);

      if (pageNum >= categories.length - 1) {
        $(".table-page-next").prop("disabled", true);
      } else {
        $(".table-page-next").prop("disabled", false);
      }
      $("#pageNumber").html(parseInt(pageNum) + 1);
      let category_text_custom = $("#category_text_custom");
      category_text_custom.text(removeTags(categories[0]["category_details"]));
      if (pageNum >= categories.length - 1) {
        nextCategory();
      }
    }
  }

  $(".table-page-next").click(function () {
    if (!$(this).is(":disabled")) {
      let categories = JSON.parse($("#available_categories").val());
      let current_pos = parseInt($(this).data("category_pos"));

      $(this)
        .parents(".table-fixed-header")
        .find("tbody")
        .fadeOut(200, function () {
          let q_count = 0;
          $(this)
            .parents(".table-fixed-header")
            .find("tbody tr")
            .each(function (index) {
              if (
                categories[current_pos]["category_id"] ==
                $(this).data("category_id")
              ) {
                $(this).css("display", "table-row");
                q_count++;
                $(this).find(".question-number").text(q_count);

                //Check follow-up question and show
                let follow_up_type;
                if ($(this).data("question_type") == "yes-no") {
                  if (
                    $(this)
                      .find("input[type=radio]:checked")
                      .hasClass("yes-check")
                  )
                    follow_up_type = "yes";
                  else if (
                    $(this)
                      .find("input[type=radio]:checked")
                      .hasClass("no-check")
                  )
                    follow_up_type = "no";
                } else if ($(this).data("question_type") == "mcq") {
                  if (
                    $(this).find("input[type=radio]:checked").data("tip") ==
                    "yes"
                  )
                    follow_up_type = "yes";
                  if (
                    $(this).find("input[type=radio]:checked").data("tip") ==
                    "no"
                  )
                    follow_up_type = "no";
                }

                if (follow_up_type == "yes") {
                  let follow_up_id = parseInt(
                    $(this).data("question_yes_follow_up")
                  );
                  if (follow_up_id) {
                    let _this = $(this);
                    $(this)
                      .parents("table")
                      .find(".follow-up")
                      .each(function () {
                        let q_id = parseInt($(this).attr("id").substring(9));
                        if (q_id == follow_up_id) {
                          let follow_up = $(this).clone();
                          _this.after(follow_up);
                          follow_up.find(".question-number").text("-");
                          follow_up.fadeIn();
                          $(this).remove();
                        }
                      });
                  }
                } else if (follow_up_type == "no") {
                  let follow_up_id = parseInt(
                    $(this).data("question_no_follow_up")
                  );
                  if (follow_up_id) {
                    let _this = $(this);
                    $(this)
                      .parents("table")
                      .find(".follow-up")
                      .each(function () {
                        let q_id = parseInt($(this).attr("id").substring(9));
                        if (q_id == follow_up_id) {
                          let follow_up = $(this).clone();
                          _this.after(follow_up);
                          follow_up.find(".question-number").text("-");
                          follow_up.fadeIn();
                          $(this).remove();
                        }
                      });
                  }
                }
              } else $(this).css("display", "none");
            });
          $(this).fadeIn(200);
        });

      let page_number = $(".table-page-number");
      page_number.html(
        categories[current_pos]["category_name"] +
          '   <i class="fas fa-info-circle"></i>'
      );
      page_number.data("category_pos", current_pos);
      let category_text_custom = $("#category_text_custom");
      category_text_custom.text(
        removeTags(categories[current_pos]["category_details"])
      );
      category_text_custom.data("category_pos", current_pos);
      $(".table-page-next").data("category_pos", current_pos + 1);
      $(".table-page-prev").data("category_pos", current_pos - 1);

      if (current_pos <= 0) {
        $(".table-page-prev").prop("disabled", true);
      } else {
        $(".table-page-prev").prop("disabled", false);
      }
      if (current_pos >= categories.length - 1) {
        $(this).prop("disabled", true);
      } else {
        $(this).prop("disabled", false);
      }
      $("#pageNumber").html(current_pos + 1);
    }
  });

  $(".table-page-prev").click(function () {
    if (!$(this).is(":disabled")) {
      let categories = JSON.parse($("#available_categories").val());
      let current_pos = parseInt($(this).data("category_pos"));

      $(this)
        .parents(".table-fixed-header")
        .find("tbody")
        .fadeOut(200, function () {
          let q_count = 0;
          $(this)
            .parents(".table-fixed-header")
            .find("tbody tr")
            .each(function (index) {
              if (
                categories[current_pos]["category_id"] ==
                $(this).data("category_id")
              ) {
                $(this).css("display", "table-row");
                q_count++;
                $(this).find(".question-number").text(q_count);

                //Check follow-up question and show
                let follow_up_type;
                if ($(this).data("question_type") == "yes-no") {
                  if (
                    $(this)
                      .find("input[type=radio]:checked")
                      .hasClass("yes-check")
                  )
                    follow_up_type = "yes";
                  else if (
                    $(this)
                      .find("input[type=radio]:checked")
                      .hasClass("no-check")
                  )
                    follow_up_type = "no";
                } else if ($(this).data("question_type") == "mcq") {
                  if (
                    $(this).find("input[type=radio]:checked").data("tip") ==
                    "yes"
                  )
                    follow_up_type = "yes";
                  if (
                    $(this).find("input[type=radio]:checked").data("tip") ==
                    "no"
                  )
                    follow_up_type = "no";
                }

                if (follow_up_type == "yes") {
                  let follow_up_id = parseInt(
                    $(this).data("question_yes_follow_up")
                  );
                  if (follow_up_id) {
                    let _this = $(this);
                    $(this)
                      .parents("table")
                      .find(".follow-up")
                      .each(function () {
                        let q_id = parseInt($(this).attr("id").substring(9));
                        if (q_id == follow_up_id) {
                          let follow_up = $(this).clone();
                          _this.after(follow_up);
                          follow_up.find(".question-number").text("-");
                          follow_up.fadeIn();
                          $(this).remove();
                        }
                      });
                  }
                } else if (follow_up_type == "no") {
                  let follow_up_id = parseInt(
                    $(this).data("question_no_follow_up")
                  );
                  if (follow_up_id) {
                    let _this = $(this);
                    $(this)
                      .parents("table")
                      .find(".follow-up")
                      .each(function () {
                        let q_id = parseInt($(this).attr("id").substring(9));
                        if (q_id == follow_up_id) {
                          let follow_up = $(this).clone();
                          _this.after(follow_up);
                          follow_up.find(".question-number").text("-");
                          follow_up.fadeIn();
                          $(this).remove();
                        }
                      });
                  }
                }
              } else $(this).css("display", "none");
            });
          $(this).fadeIn(200);
        });

      let page_number = $(".table-page-number");
      page_number.html(
        categories[current_pos]["category_name"] +
          '   <i class="fas fa-info-circle"></i>'
      );
      page_number.data("category_pos", current_pos);
      let category_text_custom = $("#category_text_custom");
      category_text_custom.text(
        removeTags(categories[current_pos]["category_details"])
      );
      category_text_custom.data("category_pos", current_pos);
      $(".table-page-next").data("category_pos", current_pos + 1);
      $(".table-page-prev").data("category_pos", current_pos - 1);

      if (current_pos <= 0) {
        $(this).prop("disabled", true);
      } else {
        $(this).prop("disabled", false);
      }
      if (current_pos >= categories.length) {
        $(".table-page-next").prop("disabled", true);
      } else {
        $(".table-page-next").prop("disabled", false);
      }

      $("#pageNumber").html(current_pos + 1);
    }
  });

  //   setProgBar();

  function unAnseredQuestionFocus() {
    $("#unanseredCatQues").show();
    $("#unanseredCatQues").focus();
  }

  $("#close-unanseredCatQues").click(function (event) {
    event.preventDefault();
    $("#unanseredCatQues").hide();
  });

  function nextCategory() {
    if ($("#btnNextQgroupAlt").length) {
      setTimeout(function () {
        if ($("#btnNextQgroup").prop("disabled")) {
          $("#btnNextQgroupAlt").prop("disabled", false);
          $("#btnNextQgroupAlt").show();
          $("#btnNextQgroup").hide();
        } else {
          $("#btnNextQgroupAlt").hide();
        }
      }, 100);
    }
  }

  //Footer language change
  $("#footer_language_selector").change(function () {
    let lang_code = $(this).val();
    let site = $(this).data("site-url");

    $.ajax({
      url: site + "/option_server.php",
      type: "POST",
      data: { sign: "language_change", lang_code: lang_code },
    }).done(function (data) {
      if (data == "success") window.location.reload();
      else alert(data);
    });
  });

  $("#close-unanseredCatQues").click(function (event) {
    event.preventDefault();
    $("#unanseredCatQues").hide();
  });