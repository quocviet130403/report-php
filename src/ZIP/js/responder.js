
var site_url = '';

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
    // $("#unanseredCatQues").modal('show');
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
	
	saveData();
  }

  //Question table
  $(".question-table tbody tr").hover(
    function () {
      let question_id = $(this).attr("id").substring(9);
      let yes_check = $(this).find(".yes-check");
      let no_check = $(this).find(".no-check");
      let mcq_check = $(this).find(".mcq-check:checked");
    },
    function () {
      $(this).find(".question-tip").remove();
    }
  );

  //On checkbox clicked
  $(".question-table tbody tr td input[type=radio]").on("change", function () {
    let class_name;
    if ($(this).hasClass("yes-check")) class_name = "yes-check";
    else if ($(this).hasClass("mcq-check")) class_name = "mcq-check";
    else class_name = "no-check";

    //Adding or removing Tips.
    if ($(this).is(":checked")) {
      $(this).parents("tbody").find(".question-tip").remove();

      let question_id = $(this).parents("tr").attr("id").substring(9);
    } else {
      $(this).parents("tbody").find(".question-tip").remove();
    }

    //Check follow-up question and show
    let follow_up_type;
    if ($(this).parents("tr").data("question_type") == "yes-no") {
      if ($(this).hasClass("yes-check")) follow_up_type = "yes";
      else follow_up_type = "no";
    } else if ($(this).parents("tr").data("question_type") == "mcq") {
      if ($(this).data("tip") == "yes") follow_up_type = "yes";
      if ($(this).data("tip") == "no") follow_up_type = "no";
      if ($(this).data("tip") == "") {
        if ($(this).parents("tr").next().hasClass("follow-up")) {
          $(this).parents("tr").next().fadeOut();
        }
      }
    }

    if (follow_up_type == "yes") {
      let follow_up_id = parseInt(
        $(this).parents("tr").data("question_yes_follow_up")
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
              if (_this.parents("tr").next().hasClass("follow-up")) {
                _this.parents("tr").next().remove();
              }
              _this.parents("tr").after(follow_up);
              follow_up.find(".question-number").text("-");
              follow_up.fadeIn();
              $(this).remove();
            }
          });
      } else {
        if ($(this).parents("tr").next().hasClass("follow-up")) {
          $(this).parents("tr").next().fadeOut();
        }
      }
    } else if (follow_up_type == "no") {
      let follow_up_id = parseInt(
        $(this).parents("tr").data("question_no_follow_up")
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
              if (_this.parents("tr").next().hasClass("follow-up")) {
                _this.parents("tr").next().remove();
              }
              _this.parents("tr").after(follow_up);
              follow_up.find(".question-number").text("-");
              follow_up.fadeIn();
              $(this).remove();
            }
          });
      } else {
        if ($(this).parents("tr").next().hasClass("follow-up")) {
          $(this).parents("tr").next().fadeOut();
        }
      }
    }
  });

  //On Select Selected
  $(".question-table tbody tr td select").on("change", function () {
    var selectedOption = $(this).find("option:selected");

    //Check follow-up question and show
    let follow_up_type;
    if (selectedOption.parents("tr").data("question_type") == "yes-no") {
      if (selectedOption.hasClass("yes-check")) follow_up_type = "yes";
      else follow_up_type = "no";
    } else if (selectedOption.parents("tr").data("question_type") == "mcq") {
      if (selectedOption.data("tip") == "yes") follow_up_type = "yes";
      if (selectedOption.data("tip") == "no") follow_up_type = "no";
      if (selectedOption.data("tip") == "") {
        if (selectedOption.parents("tr").next().hasClass("follow-up")) {
          selectedOption.parents("tr").next().fadeOut();
        }
      }
    }

    // console.log(selectedOption.data("tip"));

    // console.log("follow_up_type :" + follow_up_type);

    if (follow_up_type == "yes") {
      let follow_up_id = parseInt(
        $(this).parents("tr").data("question_yes_follow_up")
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
              if (_this.parents("tr").next().hasClass("follow-up")) {
                _this.parents("tr").next().remove();
              }
              _this.parents("tr").after(follow_up);
              follow_up.find(".question-number").text("-");
              follow_up.fadeIn();
              $(this).remove();
            }
          });
      } else {
        if ($(this).parents("tr").next().hasClass("follow-up")) {
          $(this).parents("tr").next().fadeOut();
        }
      }
    } else if (follow_up_type == "no") {
      let follow_up_id = parseInt(
        $(this).parents("tr").data("question_no_follow_up")
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
              if (_this.parents("tr").next().hasClass("follow-up")) {
                _this.parents("tr").next().remove();
              }
              _this.parents("tr").after(follow_up);
              follow_up.find(".question-number").text("-");
              follow_up.fadeIn();
              $(this).remove();
            }
          });
      } else {
        if ($(this).parents("tr").next().hasClass("follow-up")) {
          $(this).parents("tr").next().fadeOut();
        }
      }
    }
  });

  //Activation Tips
  $(".question-table tbody").on("click", ".question-tip", function () {
    let question_number = $(this).parents("tr").attr("id").substring(9);
    let check_name;
    if ($(this).siblings(".yes-check")) check_name = "yes-check";
    else check_name = "no-check";

    $(this).parents("td").find(".tip-view-ctn").fadeIn(200);
  });

  function setProgBarSelect(questionNo) {
    let progressbar = $("#progressbar");

    let pageNumber = $("#pageNumber").val();

    let percent = 0;
    let numberOfQuestionSelect = 0;

    let numberOfAnswerSelect = $("select.mcq-check").filter(function () {
      return $(this).val() !== "0";
    }).length;

    let questionIds = $("#questionIds").val();

    if (questionIds.length >= 1) {
      var questionIdArr = questionIds.split(",");
      numberOfQuestionSelect = questionIdArr.length;
    }

    percent = Math.floor((numberOfAnswerSelect * 100) / numberOfQuestionSelect);

    if (numberOfAnswerCheck == questionIdsCheck) {
      var submitText = $("#save_ticket").data("submit-text");
      $("#save_ticket").text(submitText);
    }

    progressbar.css({
      width: percent + "%",
    });

    $("#label-progressbar").html(
      percent +
        "% (" +
        numberOfAnswerSelect +
        "/" +
        numberOfQuestionSelect +
        ")"
    );
  }

  function setProgBar(questionNo) {
    let progressbar = $("#progressbar");
    let pageNumber = $("#pageNumber").val();

    let percent = 0;
    let questionIdsCheck = 0;

    let numberOfAnswerCheck = $('input.mcq-check[type="radio"]:checked').length;

    let questionIds = $("#questionIds").val();

    if (questionIds.length >= 1) {
      var questionIdArr = questionIds.split(",");
      questionIdsCheck = questionIdArr.length;
    }

    percent = Math.floor((numberOfAnswerCheck * 100) / questionIdsCheck);

    progressbar.css({
      width: percent + "%",
    });

    if (numberOfAnswerCheck == questionIdsCheck) {
      var submitText = $("#save_ticket").data("submit-text");
      $("#save_ticket").text(submitText);
    }

    $("#label-progressbar").html(
      percent + "% (" + numberOfAnswerCheck + "/" + questionIdsCheck + ")"
    );
  }

  $(document).on("change", "select", function () {
    var name = $(this).attr("name");
    var value = $(this).val();

    $('input[type=radio][name="' + name + '"][value="' + value + '"]').prop(
      "checked",
      true
    );
  });

  $(document).on("change", "input[type=radio]", function () {
    var name = $(this).attr("name");
    var value = $(this).val();

    $('select[name="' + name + '"]').val(value);
  });

  $(".tip-view").click(function () {
    $(this).parent(".tip-view-ctn").fadeOut(200);
  });

  //Footer language change
  $("#footer_language_selector").change(function () {
    let lang_code = $(this).val();

    $.ajax({
      url: site_url + "/option_server.php",
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

	$("#save_ticket").click(function (event) {
		saveData('alert');
  
     
  
	});



	function saveData($alert = null) {
		let response = {};
		let ticket_id = $('body').data("ticket_id");
		let responderID = $('body').data("responder_id");
		let site = site_url+ "/option_server.php";

		var questionIdArr = $("#questionIds").val().split(",");
		numberOfQuestion = questionIdArr.length;

		let numberOfAnswerCheck = $('input.mcq-check[type="radio"]:checked').length;

		var submitPopText = $('body').data("submit-popup");
		var submitSaveText = $('body').data("submit-save-text");
		var submitText = $('body').data("submit-text");
		var cancelText = $('body').data("cancel-text");

		let validation = true;

		$(".question-row").each(function () {
			let question_id = $(this).data("question_id");
			let answer;
			let q_type = $(this).data("question_type");
			let q_follow_up = $(this).data("question_follow_up");
			let q_yes_follow_up = $(this).data("question_yes_follow_up");
			let q_no_follow_up = $(this).data("question_no_follow_up");
			let q_notes = $("#txtnotes" + question_id).val();

			if ($(this).data("question_type") == "mcq") {
			if ($(this).find(".check_1").is(":checked")) answer = 1;
			else if ($(this).find(".check_2").is(":checked")) answer = 2;
			else if ($(this).find(".check_3").is(":checked")) answer = 3;
			else if ($(this).find(".check_4").is(":checked")) answer = 4;
			else if ($(this).find(".check_5").is(":checked")) answer = 5;
			else if ($(this).find(".check_6").is(":checked")) answer = 6;
			else answer = 0;
			} else if ($(this).data("question_type") == "yes-no") {
			if ($(this).find(".yes-check").is(":checked")) answer = 2;
			else if ($(this).find(".no-check").is(":checked")) answer = 1;
			else answer = 0;
			}

			response[question_id] = {
			answer: answer,
			type: q_type,
			"follow-up": q_follow_up,
			"yes-follow-up": q_yes_follow_up,
			"no-follow-up": q_no_follow_up,
			notes: q_notes,
			};
		});

		response = JSON.stringify(response);

		let dataRes = {};

		if (numberOfQuestion == numberOfAnswerCheck) {
			Swal.fire({
			text: submitPopText,
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			cancelButtonText: cancelText,
			confirmButtonText: submitText,
			}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
				url: site,
				type: "POST",
				data: {
					sign: "save_responder_ticket",
					response: response,
					responder_id: responderID,
					ticket_id: ticket_id,
				},
				}).done(function (data) {
				
					// console.log(data);
				
				data = JSON.parse(data);

				if (data["status"] == "success") {
					window.location.reload();
				} else {
					alert(data["message"]);
				}
				});
			}
			});
		} else {
			$.ajax({
			url: site,
			type: "POST",
			data: {
				sign: "save_responder_ticket",
				response: response,
				responder_id: responderID,
				ticket_id: ticket_id,
			},
			}).done(function (data) {
			// console.log(data);
			data = JSON.parse(data);
			if (data["status"] == "success") {

				if($alert) {
					swal.fire({
						icon: "success",
						text: submitSaveText,
						confirmButtonColor: "#3085d6",
						confirmButtonText: "Lukk",
					});
				}

			} else {
				alert(data["message"]);
			}
			});
		}
  	}
// });


function setProgBarOnLoad() {
    let responderID = $(this).data("responder-id");

    let progressbar = $("#progressbar");
    let questionIds = $("#questionIds").val();
    let answerIds = $("#answerIds").val();
    let pageNumber = $("#pageNumber").val();
    let questionNo = responderID;

    let numberOfQuestion = 0;
    let numberOfAnswer = 0;
    let percent = 0;

    if (questionIds.length >= 1) {
      var questionIdArr = questionIds.split(",");
      numberOfQuestion = questionIdArr.length;
    }

    if (answerIds.length >= 1) {
      var totalAnswerIdsArr = answerIds.split(",");
      numberOfAnswer = totalAnswerIdsArr.length;

      if (questionNo != 0) {
        if (!totalAnswerIdsArr.includes(questionNo)) {
          numberOfAnswer = numberOfAnswer;
          answerIds = answerIds + "," + questionNo;
          $("#answerIds").val(answerIds);
        }
      }
    } else {
      if (questionNo != 0) {
        $("#answerIds").val(questionNo);
        numberOfAnswer = numberOfAnswer;
      }
    }

    numberOfAnswerCheck = $('input.mcq-check[type="radio"]:checked').length;

    if (numberOfAnswerCheck == numberOfQuestion) {
      var submitText = $("#save_ticket").data("submit-text");
      $("#save_ticket").text(submitText).addClass("btn-warning");
    }

    percent = Math.floor((numberOfAnswer * 100) / numberOfQuestion);

    progressbar.css({
      width: percent + "%",
    });

    $("#label-progressbar").html(
      percent + "% (" + numberOfAnswer + "/" + numberOfQuestion + ")"
    );
  }

  setProgBarOnLoad();