var site_url = "";
var textareaId = ''; 
$(".hover-effect").click(function() {
  var clickedParentDiv = $(this);
  var textarea = clickedParentDiv.find("textarea");
  
  if (textarea.length > 0) {
      textareaId = textarea.attr("id"); // Remove 'var' to update the outer variable
    
     
  } else {
     
  }
});

// Now you can use textareaId in other functions or places
console.log("Outside click function:", textareaId);
$('.ques-loader').hide();
//Set chart background
const customCanvasBackgroundColor = {
  id: "customCanvasBackgroundColor",
  beforeDraw: (chart, args, options) => {
    const { ctx } = chart;
    ctx.save();
    ctx.globalCompositeOperation = "destination-over";
    ctx.fillStyle = options.color || "#FFffff";
    ctx.fillRect(0, 0, chart.width, chart.height);
    ctx.restore();
  },
};

$(document).ready(function () {
  var chart_1_;
  var vEditor;

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

  //Nav opener
  $("#nav-opener").click(function () {
    if ($(".user-nav").is(":visible")) {
      $(".user-nav").slideUp(200);
      $("#nav-opener").find("i").removeClass("fa-times").addClass("fa-bars");
    } else {
      $(".user-nav").slideDown(200);
      $("#nav-opener").find("i").removeClass("fa-bars").addClass("fa-times");
      window.scrollTo(0, 0);
    }
  });

  //Navigation sign out
  $(".navbar_signout").click(function () {
    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: { sign: "account_logout" },
    }).done(function (data) {
      window.location.reload();
    });
  });

  //Admin profile actions
  //Admin name change
  $("#admin_name_editor_button").click(function () {
    let old_value = $(this).parents("#admin_name_editor").data("value");
    let html =
      `
        <form class='form-inline'>
            <input type='text' id='admin_name_editor_input' class='form-control form-control-lg form-control-solid mb-3 mb-lg-0'` +
      `value='` +
      old_value.toString() +
      `'>
            <button id='admin_name_editor_save' class='btn btn-success btn-sm ml-2'><i class='fas fa-save'></i></button>
        </form>
        `;
    $(this).parents("#admin_name_editor").html(html);
  });
  $("#admin_name_editor").on(
    "click",
    "#admin_name_editor_save",
    function (event) {
      event.preventDefault();
      let admin_id = $(this).parents("#admin_name_editor").data("admin");
      let new_name_input = $(this)
        .parents("#admin_name_editor")
        .find("#admin_name_editor_input");
      let new_name = new_name_input.val();
      if (new_name.length < 1)
        new_name_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      else {
        $.ajax({
          url: site_url + "/option_server.php",
          type: "POST",
          data: {
            sign: "change_admin_name",
            admin_id: admin_id,
            new_name: new_name,
          },
        }).done(function (data) {
          window.location.reload();
        });
      }
    }
  );
  //Admin email change
  $("#admin_email_editor_button").click(function () {
    let old_value = $(this).parents("#admin_email_editor").data("value");
    let html =
      `
        <form class='form-inline'>
            <input type='email' id='admin_email_editor_input' class='form-control form-control-lg form-control-solid mb-3 mb-lg-0'` +
      `value='` +
      old_value.toString() +
      `'>
            <button id='admin_email_editor_save' class='btn btn-success btn-sm ml-2'><i class='fas fa-save'></i></button>
        </form>
        `;
    $(this).parents("#admin_email_editor").html(html);
  });
  $("#admin_email_editor").on(
    "click",
    "#admin_email_editor_save",
    function (event) {
      event.preventDefault();
      let admin_id = $(this).parents("#admin_email_editor").data("admin");
      let new_email_input = $(this)
        .parents("#admin_email_editor")
        .find("#admin_email_editor_input");
      let new_email = new_email_input.val();

      if (!validateEmail(new_email))
        new_email_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      else {
        $.ajax({
          url: site_url + "/option_server.php",
          type: "POST",
          data: {
            sign: "change_admin_email",
            admin_id: admin_id,
            new_email: new_email,
          },
        }).done(function (data) {
          if (data == "success") window.location.reload();
          else alert(data);
        });
      }
    }
  );
  //Admin Password change
  $("#admin_pass_change").click(function () {
    let html = `
        <form class='form-inline'>
        <input type='password' id='admin_pass_editor_old' class='form-control form-control-lg form-control-solid mb-3 mb-lg-0' placeholder='${$(
          "#translation"
        ).data("user_js_phrase1")}'>
            <input type='password' id='admin_pass_editor_new' class='form-control form-control-lg form-control-solid mb-3 mb-lg-0' placeholder='${$(
              "#translation"
            ).data("user_js_phrase2")}'>
            <input type='password' id='admin_pass_editor_confirm' class='form-control form-control-lg form-control-solid mb-3 mb-lg-0' placeholder='${$(
              "#translation"
            ).data("user_js_phrase3")}'>
            <button id='admin_pass_save' class='btn btn-success btn-sm ml-2'><i class='fas fa-save'></i></button>
        </form>
        `;
    $(this).parents("#admin_pass_editor").html(html);
  });
  $("#admin_pass_editor").on("click", "#admin_pass_save", function (event) {
    event.preventDefault();
    let admin_id = $(this).parents("#admin_pass_editor").data("admin");
    let old_pass_input = $(this)
      .parents("#admin_pass_editor")
      .find("#admin_pass_editor_old");
    let new_pass_input = $(this)
      .parents("#admin_pass_editor")
      .find("#admin_pass_editor_new");
    let confirm_pass_input = $(this)
      .parents("#admin_pass_editor")
      .find("#admin_pass_editor_confirm");

    let old_pass = old_pass_input.val();
    let new_pass = new_pass_input.val();
    let confirm_pass = confirm_pass_input.val();
    let validation = true;

    if (new_pass.length < 1) {
      new_pass_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }
    if (confirm_pass.length < 1) {
      confirm_pass_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }
    if (old_pass.length < 1) {
      old_pass_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }
    if (new_pass != confirm_pass) {
      confirm_pass_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }
    if (validation) {
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "admin_password_change",
          admin_id: admin_id,
          old_pass: old_pass,
          new_pass: new_pass,
        },
      }).done(function (data) {
        alert(data);
        window.location.reload();
      });
    }
  });

  //Profile actions
  //Profile name change
  $("#user_name_editor_button").click(function () {
    let html = `
        <form class='form-inline'>
            <input type='text' id='user_name_editor_input' class='form-control form-control-sm'>
            <button id='user_name_editor_save' class='btn btn-success btn-sm ml-2'><i class='fas fa-save'></i></button>
        </form>
        `;
    $(this).parents("#user_name_editor").html(html);
  });
  $("#user_name_editor").on(
    "click",
    "#user_name_editor_save",
    function (event) {
      event.preventDefault();
      let user_id = $(this).parents("#user_name_editor").data("user");
      let new_name = $(this)
        .parents("#user_name_editor")
        .find("#user_name_editor_input")
        .val();
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "change_user_name",
          user_id: user_id,
          new_name: new_name,
        },
      }).done(function (data) {
        window.location.reload();
      });
    }
  );
  //Profile email change
  $("#user_email_editor_button").click(function () {
    let html = `
        <form class='form-inline'>
            <input type='text' id='user_email_editor_input' class='form-control form-control-sm'>
            <button id='user_email_editor_save' class='btn btn-success btn-sm ml-2'><i class='fas fa-save'></i></button>
        </form>
        `;
    $(this).parents("#user_email_editor").html(html);
  });
  $("#user_email_editor").on(
    "click",
    "#user_email_editor_save",
    function (event) {
      event.preventDefault();
      let user_id = $(this).parents("#user_email_editor").data("user");
      let new_name = $(this)
        .parents("#user_email_editor")
        .find("#user_email_editor_input")
        .val();
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "change_user_email",
          user_id: user_id,
          new_email: new_name,
        },
      }).done(function (data) {
        if (data == "success") window.location.reload();
        else alert(data);
      });
    }
  );
  //Profile Password change by admin
  $("#user_pass_change_admin").click(function () {
    let html = `
        <form class='form-inline'>
            <input type='password' id='user_pass_editor_new' class='form-control form-control-sm ml-1 mb-1' placeholder='${$(
              "#translation"
            ).data("user_js_phrase2")}'>
            <input type='password' id='user_pass_editor_confirm' class='form-control form-control-sm ml-1 mb-1' placeholder='${$(
              "#translation"
            ).data("user_js_phrase3")}'>
            <button id='user_pass_save_admin' class='btn btn-success btn-sm ml-2'><i class='fas fa-save'></i></button>
        </form>
        `;
    $(this).parents("#user_pass_editor").html(html);
  });
  $("#user_pass_editor").on("click", "#user_pass_save_admin", function (event) {
    event.preventDefault();
    let user_id = $(this).parents("#user_pass_editor").data("user");
    let new_pass_input = $(this)
      .parents("#user_pass_editor")
      .find("#user_pass_editor_new");
    let confirm_pass_input = $(this)
      .parents("#user_pass_editor")
      .find("#user_pass_editor_confirm");

    let new_pass = new_pass_input.val();
    let confirm_pass = confirm_pass_input.val();
    let validation = true;

    if (new_pass.length < 1) {
      new_pass_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }
    if (confirm_pass.length < 1) {
      confirm_pass_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }
    if (new_pass != confirm_pass) {
      confirm_pass_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }
    if (validation) {
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "user_password_change_admin",
          user_id: user_id,
          new_pass: new_pass,
        },
      }).done(function (data) {
        alert(data);
        window.location.reload();
      });
    }
  });
  //Profile Password change by user
  $("#user_pass_change").click(function () {
    let html = `
        <form class='form-inline'>
        <input type='password' id='user_pass_editor_old' class='form-control form-control-sm ml-1 mb-1' placeholder='${$(
          "#translation"
        ).data("user_js_phrase1")}'>
            <input type='password' id='user_pass_editor_new' class='form-control form-control-sm ml-1 mb-1' placeholder='${$(
              "#translation"
            ).data("user_js_phrase2")}'>
            <input type='password' id='user_pass_editor_confirm' class='form-control form-control-sm ml-1 mb-1' placeholder='${$(
              "#translation"
            ).data("user_js_phrase3")}'>
            <button id='user_pass_save' class='btn btn-success btn-sm ml-2'><i class='fas fa-save'></i></button>
        </form>
        `;
    $(this).parents("#user_pass_editor").html(html);
  });
  $("#user_pass_editor").on("click", "#user_pass_save", function (event) {
    event.preventDefault();
    let user_id = $(this).parents("#user_pass_editor").data("user");
    let old_pass_input = $(this)
      .parents("#user_pass_editor")
      .find("#user_pass_editor_old");
    let new_pass_input = $(this)
      .parents("#user_pass_editor")
      .find("#user_pass_editor_new");
    let confirm_pass_input = $(this)
      .parents("#user_pass_editor")
      .find("#user_pass_editor_confirm");

    let old_pass = old_pass_input.val();
    let new_pass = new_pass_input.val();
    let confirm_pass = confirm_pass_input.val();
    let validation = true;

    if (new_pass.length < 1) {
      new_pass_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }
    if (confirm_pass.length < 1) {
      confirm_pass_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }
    if (old_pass.length < 1) {
      old_pass_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }
    if (new_pass != confirm_pass) {
      confirm_pass_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;

      var errorMsg = $("#user_pass_save").data("mismatch-error");

      alert(errorMsg);
    }
    if (validation) {
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "user_password_change",
          user_id: user_id,
          old_pass: old_pass,
          new_pass: new_pass,
        },
      }).done(function (data) {
        alert(data);
        window.location.reload();
      });
    }
  });
  $("#user_calendar_revoke").click(function (event) {
    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: { sign: "remove_calendar_access" },
    }).done(function (data) {
      window.location.reload();
    });
  });

  //Support actions
  //Support email change
  $("#support_email_editor_button").click(function () {
    let html = `
        <form class='form-inline'>
            <input type='text' id='support_email_editor_input' class='form-control form-control-sm'>
            <button id='support_email_editor_save' class='btn btn-success btn-sm ml-2'><i class='fas fa-save'></i></button>
        </form>
        `;
    $(this).parents("#support_email_editor").html(html);
  });
  $("#support_email_editor").on(
    "click",
    "#support_email_editor_save",
    function (event) {
      event.preventDefault();
      let email_input = $(this)
        .parents("#support_email_editor")
        .find("#support_email_editor_input");
      let new_email = email_input.val();

      if (!validateEmail(new_email)) {
        email_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      } else {
        email_input.css("border-color", "#ced4da");
        $.ajax({
          url: site_url + "/option_server.php",
          type: "POST",
          data: { sign: "change_support_email", new_email: new_email },
        }).done(function (data) {
          if (data == "success") window.location.reload();
          else alert(data);
        });
      }
    }
  );
  //Support phone change
  $("#support_phone_editor_button").click(function () {
    let html = `
        <form class='form-inline'>
            <input type='text' id='support_phone_editor_input' class='form-control form-control-sm'>
            <button id='support_phone_editor_save' class='btn btn-success btn-sm ml-2'><i class='fas fa-save'></i></button>
        </form>
        `;
    $(this).parents("#support_phone_editor").html(html);
  });
  $("#support_phone_editor").on(
    "click",
    "#support_phone_editor_save",
    function (event) {
      event.preventDefault();
      let phone_input = $(this)
        .parents("#support_phone_editor")
        .find("#support_phone_editor_input");
      let new_phone = phone_input.val();

      if (new_phone.length < 1) {
        phone_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      } else {
        phone_input.css("border-color", "#ced4da");
        $.ajax({
          url: site_url + "/option_server.php",
          type: "POST",
          data: { sign: "change_support_phone", new_phone: new_phone },
        }).done(function (data) {
          if (data == "success") window.location.reload();
          else alert(data);
        });
      }
    }
  );
  //Support address change
  $("#support_address_editor_button").click(function () {
    let html = `
        <form class='form-inline'>
            <textarea id='support_address_editor_input' class='form-control form-control-sm'></textarea>
            <button id='support_address_editor_save' class='btn btn-success btn-sm ml-2'><i class='fas fa-save'></i></button>
        </form>
        `;
    $(this).parents("#support_address_editor").html(html);
  });
  $("#support_address_editor").on(
    "click",
    "#support_address_editor_save",
    function (event) {
      event.preventDefault();
      let address_input = $(this)
        .parents("#support_address_editor")
        .find("#support_address_editor_input");
      let new_address = address_input.val();

      if (new_address.length < 1) {
        address_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      } else {
        address_input.css("border-color", "#ced4da");
        $.ajax({
          url: site_url + "/option_server.php",
          type: "POST",
          data: { sign: "change_support_address", new_address: new_address },
        }).done(function (data) {
          if (data == "success") window.location.reload();
          else alert(data);
        });
      }
    }
  );
  //Support TinyMCE init
  var MCEConfig = {
    selector: "#support_message_content, #support_reply_text",
    height: 300,
    content_style:
      "@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');",
    font_formats:
      "Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Montserrat=Montserrat, sans-serif;Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats;",
    plugins: [
      "advlist anchor autolink codesample fullscreen help image imagetools",
      " lists link media noneditable preview",
      " searchreplace table template visualblocks wordcount",
    ],
    toolbar:
      "insertfile a11ycheck undo redo | bold italic | forecolor backcolor | codesample | alignleft aligncenter alignright alignjustify | bullist numlist | link image",
    spellchecker_dialog: true,
  };
  tinymce.init(MCEConfig);
  //Support send message
  $("#support_message_submit").click(function (event) {
    event.preventDefault();

    let subject_input = $("#support_message_subject");
    let subject = subject_input.val();
    let message = tinymce.editors["support_message_content"].getContent();

    let validation = true;
    if (subject.length < 1) {
      subject_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }
    if (message.length < 1) {
      validation = validation & false;
    }

    if (validation) {
      subject_input.css("border-color", "#ced4da");
      $(this)
        .find("i")
        .removeClass("fa-paper-plane")
        .addClass("fa-spinner fa-spin");
      $(this).prop("disabled", true);

      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "add_support_message",
          support_subject: subject,
          support_message: message,
        },
      }).done(function (data) {
        if (data == "success") {
          window.location.reload();
        } else {
          alert(data);
        }
      });
    } else alert($("#translation").data("user_js_phrase22"));
  });
  //Support send reply
  $("#support_reply_submit").click(function (event) {
    event.preventDefault();

    let support_id = $(this).data("support_id");
    let message = tinymce.editors["support_reply_text"].getContent();

    let validation = true;
    if (message.length < 1) {
      validation = validation & false;
    }

    if (validation) {
      $(this)
        .find("i")
        .removeClass("fa-paper-plane")
        .addClass("fa-spinner fa-spin");
      $(this).prop("disabled", true);

      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "add_support_reply",
          parent_id: support_id,
          support_message: message,
        },
      }).done(function (data) {
        if (data == "success") {
          window.location.reload();
        } else {
          alert(data);
        }
      });
    } else alert($("#translation").data("user_js_phrase22"));
  });

  //Company Actions
  //Suspend company
  $("#company_suspend").click(function () {
    let company_id = $(this).data("company");
    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: { sign: "suspend_company", company_id: company_id },
    }).done(function (data) {
      if (data == "success") {
        window.location.reload();
      } else {
        alert(data);
      }
    });
  });
  //Activate company
  $("#company_activate").click(function () {
    let company_id = $(this).data("company");
    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: { sign: "activate_company", company_id: company_id },
    }).done(function (data) {
      if (data == "success") {
        window.location.reload();
      } else {
        alert(data);
      }
    });
  });
  //Renew company
  $("#company_renew").click(function () {
    let company_id = $(this).data("company");
    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: { sign: "renew_company", company_id: company_id },
    }).done(function (data) {
      if (data == "success") {
        window.location.reload();
      } else {
        alert(data);
      }
    });
  });
  //Delete company
  $("#company_delete").click(function () {
    let company_id = $(this).data("company_id");
    alert(company_id);

    let confirm_delete = confirm($("#translation").data("user_js_phrase24"));
    if (confirm_delete) {
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: { sign: "delete_company", company_id: company_id },
      }).done(function (data) {
        if (data == "success") {
          window.location.href = site_url + "/user/index.php?route=companies";
        } else {
          alert(data);
        }
      });
    }
  });
  //Company name change
  $("#company_name_editor_button").click(function () {
    let html = `
        <form class='form-inline'>
            <input type='text' id='company_name_editor_input' class='form-control form-control-sm'>
            <button id='company_name_editor_save' class='btn btn-success btn-sm ml-2'><i class='fas fa-save'></i></button>
        </form>
        `;
    $(this).parents("#company_name_editor").html(html);
  });
  $("#company_name_editor").on(
    "click",
    "#company_name_editor_save",
    function (event) {
      event.preventDefault();
      let company_id = $(this).parents("#company_name_editor").data("company");
      let new_name_input = $(this)
        .parents("#company_name_editor")
        .find("#company_name_editor_input");
      let new_name = new_name_input.val();

      if (new_name.length < 1) new_name_input.css("border", "1px solid red");
      else {
        $.ajax({
          url: site_url + "/option_server.php",
          type: "POST",
          data: {
            sign: "change_company_name",
            company_id: company_id,
            new_name: new_name,
          },
        }).done(function (data) {
          window.location.reload();
        });
      }
    }
  );
  //Company email change
  $("#company_email_editor_button").click(function () {
    let html = `
        <form class='form-inline'>
            <input type='text' id='company_email_editor_input' class='form-control form-control-sm'>
            <button id='company_email_editor_save' class='btn btn-success btn-sm ml-2'><i class='fas fa-save'></i></button>
        </form>
        `;
    $(this).parents("#company_email_editor").html(html);
  });
  $("#company_email_editor").on(
    "click",
    "#company_email_editor_save",
    function (event) {
      event.preventDefault();
      let company_id = $(this).parents("#company_email_editor").data("company");
      let new_email_input = $(this)
        .parents("#company_email_editor")
        .find("#company_email_editor_input");
      let new_email = new_email_input.val();

      if (!validateEmail(new_email))
        new_email_input.css("border", "1px solid red");
      else {
        $.ajax({
          url: site_url + "/option_server.php",
          type: "POST",
          data: {
            sign: "change_company_email",
            company_id: company_id,
            new_email: new_email,
          },
        }).done(function (data) {
          if (data == "success") window.location.reload();
          else alert(data);
        });
      }
    }
  );
  //Change company industry type
  //Insert form and get industry types
  $("#company_industry_type_editor").on(
    "click",
    "#company_industry_type_editor_button",
    function () {
      let html = `
        <form class='form-inline'>
            <select id='company_industry_editor_types' class='form-control form-control-sm ml-2 mt-1'>
                <option value=''>${$("#translation").data(
                  "user_js_phrase37"
                )}</option>
            </select>
            <button id='company_industry_type_editor_save' class='btn btn-success btn-sm ml-2 mt-1'><i class='fas fa-save'></i></button>
        </form>
        `;
      $(this).parents("#company_industry_type_editor").html(html);
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: { sign: "get_industry_content" },
        //data: {'sign': 'get_industry_types'}
      }).done(function (data) {
        if (data.length > 0) {
          let industry_types = JSON.parse(data);
          let options = "";
          for (let key in industry_types) {
            options += `
                        <option value="${industry_types[key]["industry_id"]}">
                            ${industry_types[key]["industry_name"]}
                        </option>
                    `;
          }
          $("#company_industry_editor_types").append(options);
        }
      });
    }
  );
  $("#company_industry_type_editor").on(
    "click",
    "#company_industry_type_editor_save",
    function (event) {
      event.preventDefault();
      let company_id = $(this)
        .parents("#company_industry_type_editor")
        .data("company");
      let industry_type_input = $(this)
        .parents("#company_industry_type_editor")
        .find("#company_industry_editor_types");
      let industry_type = industry_type_input.val();

      if (!industry_type) industry_type_input.css("border", "1px solid red");
      else {
        $.ajax({
          url: site_url + "/option_server.php",
          type: "POST",
          data: {
            sign: "change_company_industry",
            company_id: company_id,
            industry_id: industry_type,
          },
        }).done(function (data) {
          if (data == "success") window.location.reload();
          else alert(data);
        });
      }
    }
  );
  //Change company plan

  showmoduleoption();
  //Insert form and get package classes
  $("#company_plan_editor").on(
    "click",
    "#company_plan_editor_button",
    function () {
      let html = `
        <form class='form-inline'>
            <select id='company_plan_editor_classes' class='form-control form-control-sm ml-2 mt-1'>
                <option value=''>${$("#translation").data(
                  "user_js_phrase26"
                )}</option>
            </select>
            <button id='company_plan_editor_save' class='btn btn-success btn-sm ml-2 mt-1'><i class='fas fa-save'></i></button>
        </form>
        `;
      $(this).parents("#company_plan_editor").html(html);
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: { sign: "get_package_class" },
      }).done(function (data) {
        if (data.length > 0) {
          let packages = JSON.parse(data);
          let options = "";
          for (let key in packages) {
            options += `
                        <option value={"min":"${
                          packages[key]["package_size_min"]
                        }","max":"${packages[key]["package_size_max"]}"}>
                            ${packages[key]["package_size_min"]} - ${
              packages[key]["package_size_max"]
            } ${$("#translation").data("user_js_phrase28")}
                        </option>
                    `;
          }
          $("#company_plan_editor_classes").append(options);
        }
      });
    }
  );

  //Update packages based on package classes
  $("#company_plan_editor").on(
    "change",
    "#company_plan_editor_classes",
    function () {
      let min_max = $(this).val();
      let plan_editor = $("#company_plan_editor");

      $(this).css("border-color", "#ced4da");

      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: { sign: "package_updater", min_max: min_max },
      }).done(function (data) {
        let option_array = JSON.parse(data);

        let html = '<div class="company-package-ctn">';
        for (let key in option_array) {
          html += `
                    <div class="company-single-package selectable" data-package_id="${
                      option_array[key]["package_id"]
                    }">
                        <div class="price">
                            <br> <span class="value">${plan_editor.data(
                              "site_currency_symbol"
                            )}${
            option_array[key]["price"]
          }</span><br> <span class="currency">${plan_editor.data(
            "site_currency"
          )} / ${$("#translation").data("user_js_phrase32")}</span>
                        </div>
                        <div class="name">
                            <label>${$("#translation").data(
                              "user_js_phrase34"
                            )} </label>
                            <span class="value">${
                              option_array[key]["name"]
                            }</span>
                        </div>
                        <div class="user">
                            <label>${$("#translation").data(
                              "user_js_phrase33"
                            )} </label>
                            <span class="value">${
                              option_array[key]["user"]
                            }</span>
                        </div>
                        <div class="details">
                            ${option_array[key]["details"]}
                        </div>
                    </div>
                `;
        }
        html += "</div>";
        $("#company_plan_editor").find("div.company-package-ctn").remove();
        $("#company_plan_editor").append(html);
      });
    }
  );
  $("#company_plan_editor1").on(
    "change",
    "#company_plan_editor_classes",
    function () {
      let min_max = $(this).val();
      let plan_editor = $("#company_plan_editor");

      $(this).css("border-color", "#ced4da");

      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: { sign: "package_updater", min_max: min_max },
      }).done(function (data) {
        let option_array = JSON.parse(data);

        let html = '<div class="company-package-ctn">';
        for (let key in option_array) {
          html += `
                    <div class="company-single-package selectable" data-package_id="${
                      option_array[key]["package_id"]
                    }">
                        <div class="price">
                            <br> <span class="value">${plan_editor.data(
                              "site_currency_symbol"
                            )}${
            option_array[key]["price"]
          }</span><br> <span class="currency">${plan_editor.data(
            "site_currency"
          )} / ${$("#translation").data("user_js_phrase32")}</span>
                        </div>
                        <div class="name">
                            <label>${$("#translation").data(
                              "user_js_phrase34"
                            )} </label>
                            <span class="value">${
                              option_array[key]["name"]
                            }</span>
                        </div>
                        <div class="user">
                            <label>${$("#translation").data(
                              "user_js_phrase33"
                            )} </label>
                            <span class="value">${
                              option_array[key]["user"]
                            }</span>
                        </div>
                        <div class="details">
                            ${option_array[key]["details"]}
                        </div>
                    </div>
                `;
        }
        html += "</div>";
        $("#company_plan_editor").find("div.company-package-ctn").remove();
        $("#company_plan_editor").append(html);
      });
    }
  );
  //Company package selector
  $("#company_plan_editor").on(
    "click",
    ".company-single-package.selectable",
    function () {
      $(this).addClass("selected");
      $(this)
        .siblings()
        .each(function () {
          $(this).removeClass("selected").addClass("selectable");
        });
    }
  );
  //save company package
  $("#company_plan_editor").on(
    "click",
    "#company_plan_editor_save",
    function (event) {
      event.preventDefault();
      let company_id = $(this).parents("#company_plan_editor").data("company");
      let admin_action = $(this).parents("#company_plan_editor").data("admin");
      let class_input = $("#company_plan_editor_classes");
      let button = $(this);

      let class_value = class_input.val();
      let package_value = $(".company-single-package.selected").data(
        "package_id"
      );

      let validation = true;
      if (class_value.length < 1) {
        class_input.css("border", "1px solid red");
        validation = validation & false;
      } else if (!package_value) {
        alert($("#translation").data("user_js_phrase27"));
        validation = validation & false;
      }

      if (validation) {
        let update_confirmation = false;
        if (admin_action == 1) update_confirmation = true;
        else
          update_confirmation = confirm(
            $("#translation").data("user_js_phrase29")
          );

        if (update_confirmation) {
          button
            .find("i")
            .removeClass("fa-save")
            .addClass("fa-spin fa-spinner");
          button.prop("disabled", true);
          $.ajax({
            url: site_url + "/option_server.php",
            type: "POST",
            data: {
              sign: "company_package_update",
              company_id: company_id,
              package_id: package_value,
            },
          }).done(function (data) {
            if (data == "success") {
              window.location.reload();
            } else {
              button.prop("disabled", false);
              alert(data);
            }
          });
        }
      }
    }
  );
  $("#company_plan_editor1").on(
    "click",
    "#company_plan_editor_save",
    function (event) {
      event.preventDefault();
      let company_id = $(this).parents("#company_plan_editor1").data("company");
      let admin_action = $(this).parents("#company_plan_editor1").data("admin");
      let class_input = $("#company_plan_editor_classes");
      let button = $(this);

      let class_value = class_input.val();
      let package_value = $(".company-single-package.selected").data(
        "package_id"
      );

      let validation = true;
      if (class_value.length < 1) {
        class_input.css("border", "1px solid red");
        validation = validation & false;
      } else if (!package_value) {
        alert($("#translation").data("user_js_phrase27"));
        validation = validation & false;
      }

      if (validation) {
        let update_confirmation = false;
        if (admin_action == 1) update_confirmation = true;
        else
          update_confirmation = confirm(
            $("#translation").data("user_js_phrase29")
          );

        if (update_confirmation) {
          button
            .find("i")
            .removeClass("fa-save")
            .addClass("fa-spin fa-spinner");
          button.prop("disabled", true);
          $.ajax({
            url: site_url + "/option_server.php",
            type: "POST",
            data: {
              sign: "company_package_update",
              company_id: company_id,
              package_id: package_value,
            },
          }).done(function (data) {
            if (data == "success") {
              // window.location.reload();
              var url =
                "/user/index.php?route=company_profile&company_id=" +
                company_id;
              window.location.href = url;
            } else {
              button.prop("disabled", false);
              alert(data);
            }
          });
        }
      }
    }
  );
  //Change number of company user
  $("#company_size_editor_button").click(function () {
    let html = `
        <form class='form-inline'>
            <input type='text' id='company_size_editor_input' class='form-control form-control-sm'>
            <button id='company_size_editor_save' class='btn btn-success btn-sm ml-2'><i class='fas fa-save'></i></button>
        </form>
        `;
    $(this).parents("#company_size_editor").html(html);
  });
  $("#company_size_editor").on(
    "click",
    "#company_size_editor_save",
    function (event) {
      event.preventDefault();
      let company_id = $(this).parents("#company_size_editor").data("company");
      let new_size = $(this)
        .parents("#company_size_editor")
        .find("#company_size_editor_input")
        .val();
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "change_company_size",
          company_id: company_id,
          new_size: new_size,
        },
      }).done(function (data) {
        window.location.reload();
      });
    }
  );
  //Company payment cycle change
  $("#company_payment_cycle_editor_button").click(function () {
    let html = `
        <form class='form-inline'>
            <select id='company_payment_cycle_editor_input' class='form-control form-control-sm'>
                <option value='3'>${$("#translation").data(
                  "user_js_phrase4"
                )}</option>
                <option value='6'>${$("#translation").data(
                  "user_js_phrase5"
                )}</option>
                <option value='12'>${$("#translation").data(
                  "user_js_phrase6"
                )}</option>
            </select>
            <button id='company_payment_cycle_editor_save' class='btn btn-success btn-sm ml-2'><i class='fas fa-save'></i></button>
        </form>
        `;
    $(this).parents("#company_payment_cycle_editor").html(html);
  });
  $("#company_payment_cycle_editor").on(
    "click",
    "#company_payment_cycle_editor_save",
    function (event) {
      event.preventDefault();
      let company_id = $(this)
        .parents("#company_payment_cycle_editor")
        .data("company");
      let new_cycle = $(this)
        .parents("#company_payment_cycle_editor")
        .find("#company_payment_cycle_editor_input")
        .val();
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "change_company_payment_cycle",
          company_id: company_id,
          new_cycle: new_cycle,
        },
      }).done(function (data) {
        window.location.reload();
      });
    }
  );
  //Company Ticket view
  $("#company_ticket_view_editor_button").click(function () {
    let html = `
        <form class='form-inline'>
            <select id='company_ticket_view_editor_input' class='form-control form-control-sm'>
                <option value='1'>${$("#translation").data(
                  "user_js_phrase20"
                )}</option>
                <option value='0'>${$("#translation").data(
                  "user_js_phrase21"
                )}</option>
            </select>
            <button id='company_ticket_view_editor_save' class='btn btn-success btn-sm ml-2'><i class='fas fa-save'></i></button>
        </form>
        `;
    $(this).parents("#company_ticket_view_editor").html(html);
  });
  $("#company_ticket_view_editor").on(
    "click",
    "#company_ticket_view_editor_save",
    function (event) {
      event.preventDefault();
      let company_id = $(this)
        .parents("#company_ticket_view_editor")
        .data("company");
      let ticket_view = $(this)
        .parents("#company_ticket_view_editor")
        .find("#company_ticket_view_editor_input")
        .val();
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "change_company_ticket_view",
          company_id: company_id,
          ticket_view: ticket_view,
        },
      }).done(function (data) {
        window.location.reload();
      });
    }
  );
  //Company password change by admin
  $("#company_password_editor_button_admin").click(function () {
    let html = `
        <form class='form-inline'>
            <input type='password' id='company_pass_editor_new' class='form-control form-control-sm ml-1 mb-1' placeholder='${$(
              "#translation"
            ).data("user_js_phrase2")}'>
            <input type='password' id='company_pass_editor_confirm' class='form-control form-control-sm ml-1 mb-1' placeholder='${$(
              "#translation"
            ).data("user_js_phrase3")}'>
            <button id='company_pass_editor_save_admin' class='btn btn-success btn-sm ml-2'><i class='fas fa-save'></i></button>
        </form>
        `;
    $(this).parents("#company_password_editor").html(html);
  });
  $("#company_password_editor").on(
    "click",
    "#company_pass_editor_save_admin",
    function (event) {
      event.preventDefault();
      let company_id = $(this)
        .parents("#company_password_editor")
        .data("company");
      let new_pass_input = $(this)
        .parents("#company_password_editor")
        .find("#company_pass_editor_new");
      let confirm_pass_input = $(this)
        .parents("#company_password_editor")
        .find("#company_pass_editor_confirm");

      let new_pass = new_pass_input.val();
      let confirm_pass = confirm_pass_input.val();
      let validation = true;

      if (new_pass.length < 1) {
        new_pass_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
        validation = validation & false;
      }
      if (confirm_pass.length < 1) {
        confirm_pass_input.css(
          "border-bottom",
          "3px solid rgba(160, 0, 0, .5)"
        );
        validation = validation & false;
      }
      if (new_pass != confirm_pass) {
        confirm_pass_input.css(
          "border-bottom",
          "3px solid rgba(160, 0, 0, .5)"
        );
        validation = validation & false;
      }
      if (validation) {
        $.ajax({
          url: site_url + "/option_server.php",
          type: "POST",
          data: {
            sign: "change_company_password_admin",
            company_id: company_id,
            new_pass: new_pass,
          },
        }).done(function (data) {
          alert(data);
          window.location.reload();
        });
      }
    }
  );
  //Company password change by user
  $("#company_password_editor_button").click(function () {
    let html = `
        <form class='form-inline'>
            <input type='password' id='company_pass_editor_old' class='form-control form-control-sm ml-1 mb-1' placeholder='${$(
              "#translation"
            ).data("user_js_phrase1")}'>
            <input type='password' id='company_pass_editor_new' class='form-control form-control-sm ml-1 mb-1' placeholder='${$(
              "#translation"
            ).data("user_js_phrase2")}'>
            <input type='password' id='company_pass_editor_confirm' class='form-control form-control-sm ml-1 mb-1' placeholder='${$(
              "#translation"
            ).data("user_js_phrase3")}'>
            <button id='company_pass_editor_save' class='btn btn-success btn-sm ml-2'><i class='fas fa-save'></i></button>
        </form>
        `;
    $(this).parents("#company_password_editor").html(html);
  });
  $("#company_password_editor").on(
    "click",
    "#company_pass_editor_save",
    function (event) {
      event.preventDefault();
      let company_id = $(this)
        .parents("#company_password_editor")
        .data("company");
      let old_pass_input = $(this)
        .parents("#company_password_editor")
        .find("#company_pass_editor_old");
      let new_pass_input = $(this)
        .parents("#company_password_editor")
        .find("#company_pass_editor_new");
      let confirm_pass_input = $(this)
        .parents("#company_password_editor")
        .find("#company_pass_editor_confirm");

      let new_pass = new_pass_input.val();
      let old_pass = old_pass_input.val();
      let confirm_pass = confirm_pass_input.val();
      let validation = true;

      if (old_pass.length < 1) {
        old_pass_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
        validation = validation & false;
      }
      if (new_pass.length < 1) {
        new_pass_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
        validation = validation & false;
      }
      if (confirm_pass.length < 1) {
        confirm_pass_input.css(
          "border-bottom",
          "3px solid rgba(160, 0, 0, .5)"
        );
        validation = validation & false;
      }
      if (new_pass != confirm_pass) {
        confirm_pass_input.css(
          "border-bottom",
          "3px solid rgba(160, 0, 0, .5)"
        );
        validation = validation & false;
      }
      if (validation) {
        $.ajax({
          url: site_url + "/option_server.php",
          type: "POST",
          data: {
            sign: "change_company_password",
            company_id: company_id,
            new_pass: new_pass,
            old_pass: old_pass,
          },
        }).done(function (data) {
          alert(data);
          window.location.reload();
        });
      }
    }
  );
  //Company logo change
  $("#company_logo_change").click(function () {
    $(".company_logo_input").click();
  });
  $(".company_logo_input").change(function () {
    let company_id = $(this).data("company");
    let logo_data = $(this).prop("files")[0];
    let form_data = new FormData();
    form_data.append("logo", logo_data);
    form_data.append("sign", "update_company_logo");
    form_data.append("company_id", company_id);

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      dataType: "json",
      cache: false,
      contentType: false,
      processData: false,
      data: form_data,
      beforeSend: function () {
        var $loading = $(
          '<div id="loading" style="z-index: 999;background: #FFF;color: #000;position: absolute;left: 0;right: 0;top: 0;bottom: 0;">Loading...</div>'
        );

        $("#companyLogo").append($loading);
      },
    }).done(function (data) {
      if (data["success"] == "success") {
        $("#upload_company_img").val(data["img"]);
        $("#companyLogo img").attr("src", data["url"]);
        $("#userProfileDropdown img").attr("src", data["url"]);

        $("#companyLogo #loading").remove();
      } else {
        alert(data);
      }
    });
  });

  $(".consultant_logo_input").change(function () {
    let consultant_id = $(this).data("consultant");
    let logo_data = $(this).prop("files")[0];
    let form_data = new FormData();
    form_data.append("logo", logo_data);
    form_data.append("sign", "update_consultant_logo");
    form_data.append("consultant_id", consultant_id);

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      dataType: "json",
      cache: false,
      contentType: false,
      processData: false,
      data: form_data,
    }).done(function (data) {
      if (data["success"] == "success") {
        //window.location.reload();
        $("#upload_consultant_img").val(data["img"]);
      } else {
        alert(data);
      }
    });
  });

  $(".admin_logo_input").change(function () {
    let admin_id = $(this).data("admin");
    let logo_data = $(this).prop("files")[0];
    let form_data = new FormData();
    form_data.append("logo", logo_data);
    form_data.append("sign", "update_admin_logo");
    form_data.append("admin_id", admin_id);

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      dataType: "json",
      cache: false,
      contentType: false,
      processData: false,
      data: form_data,
    }).done(function (data) {
      if (data["success"] == "success") {
        //window.location.reload();
        $("#upload_admin_img").val(data["img"]);
      } else {
        alert(data);
      }
    });
  });

  //Question tips, TinyMCE init
  // var MCEConfig = {
  //     selector: "#company_report_text_input",
  //     height: 250,
  //     plugins: [
  //         "advlist anchor autolink codesample fullscreen help image imagetools",
  //         " lists link media noneditable preview",
  //         " searchreplace table template visualblocks wordcount"
  //     ],
  //     toolbar:
  //         "insertfile a11ycheck undo redo | bold italic | forecolor backcolor | codesample | alignleft aligncenter alignright alignjustify | bullist numlist | link image",
  //     spellchecker_dialog: true,
  // };
  // tinymce.init(MCEConfig);

  // $('#company_report_text_save').click(function (event) {
  //     event.preventDefault();
  //     let editor = $('#company_report_text_input');
  //     let report_text = tinymce.editors[editor.attr('id')].getContent();
  //     let company_id = $(this).parents('#company_report_text_editor').data('company_id');
  //     let lang_code = $('#company_report_text_lang').val();

  //     $.ajax({
  //         url: site_url + '/option_server.php',
  //         type: 'POST',
  //         data: {
  //             'sign': 'report_text_update',
  //             'report_text': report_text,
  //             'company_id': company_id,
  //             'lang_code': lang_code
  //         }
  //     }).done(function (data) {
  //         if (data == 'success') {
  //             window.location.reload();
  //         }
  //         else
  //             alert(data);
  //     });
  // });
  //save company profile
  $("form#mycompanyprofile").on("submit", function (event) {
    event.preventDefault();

    let company_id = $("#company_id_editor_input").val();
    let upload_company_img = $("#upload_company_img").val();
    let new_name = $("#company_name_editor_input").val();
    let new_email = $("#company_email_editor_input").val();
    let old_email = $("#company_email_editor_old").val();

    let industry_type = $("#company_industry_editor_types").val();
    let new_cycle = $("#company_payment_cycle_editor_input").val();

    let report_text = $("#company_report_text_input").val();
    let lang_code = $("#company_report_text_lang").val();

    if (new_name.trim() == "") {
      $("#company_name_editor_input").css("border", "1px solid red");
      return false;
    }
    if (!validateEmail(new_email)) {
      $("#company_email_editor_input").css(
        "border-bottom",
        "3px solid rgba(160, 0, 0, .5)"
      );
      return false;
    }
    if (!industry_type) {
      $("#company_industry_editor_types").css("border", "1px solid red");
      return false;
    }

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "update_company_data",
        company_id: company_id,
        upload_company_img: upload_company_img,
        new_name: new_name,
        new_email: new_email,
        industry_id: industry_type,
        new_cycle: new_cycle,
        report_text: report_text,
        lang_code: lang_code,
        old_email: old_email,
      },
    }).done(function (data) {
      window.location.reload();
    });
  });

  //save admin profile
  $("form#myadminprofile").on("submit", function (event) {
    event.preventDefault();
    let admin_id = $("#admin_id_input").val();
    let upload_admin_img = $("#upload_admin_img").val();
    let new_name = $("#admin_name_editor_input").val();
    let new_email = $("#admin_email_editor_input").val();
    let old_email = $("#admin_email_editor_old").val();

    if (new_name.trim() == "") {
      $("#admin_name_editor_input").css("border", "1px solid red");
      return false;
    }
    if (!validateEmail(new_email)) {
      $("#admin_email_editor_input").css(
        "border-bottom",
        "3px solid rgba(160, 0, 0, .5)"
      );
      return false;
    }

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "update_admin_data",
        admin_id: admin_id,
        upload_admin_img: upload_admin_img,
        new_name: new_name,
        new_email: new_email,
        old_email: old_email,
      },
    }).done(function (data) {
      window.location.reload();
    });
  });

  //save consultant profile
  $("form#myconsultantprofile").on("submit", function (event) {
    event.preventDefault();
    let consultant_id = $("#consultant_id_input").val();
    let upload_consultant_img = $("#upload_consultant_img").val();
    let new_name = $("#consultant_name_editor_input").val();
    let new_email = $("#consultant_email_editor_input").val();
    let old_email = $("#consultant_email_editor_old").val();
    let consultant_status = $("#consultant_status").val();
    let companies = [];

    $(".consultant-company").each(function () {
      if ($(this).is(":checked")) {
        companies.push($(this).val());
      }
    });
    companies = companies.toString();

    if (new_name.trim() == "") {
      $("#consultant_name_editor_input").css("border", "1px solid red");
      return false;
    }
    if (!validateEmail(new_email)) {
      $("#consultant_email_editor_input").css(
        "border-bottom",
        "3px solid rgba(160, 0, 0, .5)"
      );
      return false;
    }

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "update_consultant_data",
        consultant_id: consultant_id,
        upload_consultant_img: upload_consultant_img,
        new_name: new_name,
        new_email: new_email,
        old_email: old_email,
        companies: companies,
        status: consultant_status,
      },
    }).done(function (data) {
      window.location.reload();
    });
  });
  $("#myuserprofilecustom").on("submit", function (event) {
    e.preventDefault();
    $.ajax({
      type: "POST",
      url: site_url + "/option_server.php",
      data: new FormData(this),
      dataType: "json",
      contentType: false,
      cache: false,
      processData: false,
      beforeSend: function () {
        $("#user_update_btn_submit").attr("disabled", "disabled");
        $("#myuserprofile").css("opacity", ".5");
      },
      success: function (response) {
        $(".statusMsg").html("");
        if (response.status == 1) {
          $("#myuserprofile")[0].reset();
          $(".statusMsg").html(
            '<p class="alert alert-success">' + response.message + "</p>"
          );
        } else {
          $(".statusMsg").html(
            '<p class="alert alert-danger">' + response.message + "</p>"
          );
        }
        $("#myuserprofile").css("opacity", "");
        $("#user_update_btn_submit").removeAttr("disabled");
      },
    });
  });
  //save user profile
  /*$("form#myuserprofile").on("submit", function (event) {
        event.preventDefault()
        let user_id = $('#user_id_input').val();
        let new_name = $('#user_name_editor_input').val();
        let new_email = $('#user_email_editor_input').val();
        let old_email = $('#user_email_editor_old').val();


        if (new_name.trim() == '') {
            $('#user_name_editor_input').css('border', '1px solid red');
            return false;

        }
        if (!validateEmail(new_email)) {
            $('#user_email_editor_input').css('border-bottom', '3px solid rgba(160, 0, 0, .5)');
            return false;
        }

        $.ajax({
            url: site_url + '/option_server.php',
            type: 'POST',
            data: {
                'sign': 'update_user_data',
                'user_id': user_id,
                'new_name': new_name,
                'new_email': new_email,
                'old_email': old_email
            }
        }).done(function (data) {
            window.location.reload();
        });


    });*/

  //Company User delete
  $(".user-card-delete").click(function () {
    let user_id = $(this).data("user-id");
    let delete_confirm = confirm($("#translation").data("user_js_phrase7"));

    if (delete_confirm) {
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: { sign: "company_user_delete", user_id: user_id },
      }).done(function (data) {
        if (data == "success") {
          window.location.reload();
        } else {
          alert(data);
        }
      });
    }
  });

  $("#remove_img").click(function () {
    let user_id = $("#user_id_input").val();

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: { sign: "user_delete_profileimg", user_id: user_id },
    }).done(function (data) {
      if (data == "success") {
        window.location.reload();
      } else {
        alert(data);
      }
    });
  });
  $(document).ready(function (e) {
    // Submit form data via Ajax
    $("#fupForm").on("submit", function (e) {
      e.preventDefault();
      $.ajax({
        type: "POST",
        url: site_url + "/option_server.php",
        data: new FormData(this),
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
          $(".submitBtn").attr("disabled", "disabled");
          $("#fupForm").css("opacity", ".5");
        },
        success: function (response) {
          $(".statusMsg").html("");
          if (response.status == 1) {
            $("#fupForm")[0].reset();
            $(".statusMsg").html(
              '<p class="alert alert-success">' + response.message + "</p>"
            );
          } else {
            $(".statusMsg").html(
              '<p class="alert alert-danger">' + response.message + "</p>"
            );
          }
          $("#fupForm").css("opacity", "");
          $(".submitBtn").removeAttr("disabled");
        },
      }).done(function (data) {
        window.location.reload();
      });
    });
  });
  function imagePreview(fileInput) {
    if (fileInput.files && fileInput.files[0]) {
      var fileReader = new FileReader();
      fileReader.onload = function (event) {
        $("#preview").html(
          '<img id="img" src="' +
            event.target.result +
            '" width="150" width="200" height="auto"/><br><p class="help-block">To replace this image click choose file</p>'
        );
      };
      fileReader.readAsDataURL(fileInput.files[0]);
    }
  }
  // File type validation
  $("#file").change(function () {
    var file = this.files[0];
    var fileType = file.type;
    var match = ["image/jpeg", "image/png", "image/jpg"];
    if (
      !(fileType == match[0] || fileType == match[1] || fileType == match[2])
    ) {
      alert("Sorry, only JPEG, & PNG files are allowed to upload.");
      $("#file").val("");
      return false;
    } else {
      imagePreview(this);
    }
  });

  //Language Actions
  //Add new language
  $("#new_language_button").click(function (event) {
    event.preventDefault();

    let code_input = $("#new_language_code");
    let name_input = $("#new_language_name");
    let code = code_input.val();
    let name = name_input.val();

    let validation = true;
    if (code.length < 1 || code.length > 4) {
      code_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }
    if (name.length < 1) {
      name_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }

    if (validation) {
      code_input.css("border-color", "#ced4da");
      name_input.css("border-color", "#ced4da");

      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: { sign: "add_new_language", code: code, name: name },
      }).done(function (data) {
        if (data == "success") {
          window.location.reload();
        } else {
          alert(data);
        }
      });
    }
  });
  //Delete language
  $(".delete_language").click(function () {
    let code = $(this).data("code");
    let confirm_delete = confirm($("#translation").data("user_js_phrase8"));
    if (confirm_delete) {
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: { sign: "delete_language", code: code },
      }).done(function (data) {
        if (data == "success") {
          window.location.reload();
        } else {
          alert(data);
        }
      });
    }
  });
  //Activate Language
  $(".activate_language").click(function () {
    let code = $(this).data("code");

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: { sign: "activate_language", code: code },
    }).done(function (data) {
      if (data == "success") {
        window.location.reload();
      } else {
        alert(data);
      }
    });
  });
  //Deactivate Language
  $(".deactivate_language").click(function () {
    let code = $(this).data("code");

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: { sign: "deactivate_language", code: code },
    }).done(function (data) {
      if (data == "success") {
        window.location.reload();
      } else {
        alert(data);
      }
    });
  });
  //Default Language
  $(".default_language").click(function () {
    let code = $(this).data("code");

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: { sign: "default_language", code: code },
    }).done(function (data) {
      if (data == "success") {
        window.location.reload();
      } else {
        alert(data);
      }
    });
  });
  //Edit language name
  $(".edit-language-name").click(function () {
    let lang_code = $(this).data("lang_code");
    let lang_value = $(this)
      .parent()
      .find(".language-name-value")
      .text()
      .trim();

    let html = `
        <form class="form-inline">
            <input type="text" class="form-control form-control-sm" value="${lang_value}">
            <button type="button" class="btn btn-sm btn-success ml-1 mr-1 save-language-name" data-lang_code="${lang_code}">
                <i class="fas fa-save"></i>
            </button>
        </form>
        `;

    $(this).css("display", "none");
    $(this).parent().find(".language-name-value").html(html);
  });
  $(".language-name").on("click", ".save-language-name", function () {
    let button = $(this);

    let lang_value = $(this).siblings("input[type=text]").val().trim();
    let lang_code = $(this).data("lang_code");
    if (lang_value.length > 0) {
      $(this).find("i").removeClass("fa-save").addClass("fa-spin fa-spinner");
      $(this).siblings("input[type=text]").css("border-color", "#ced4da");
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "update_language_name",
          lang_code: lang_code,
          lang_value: lang_value,
        },
      }).done(function (data) {
        button
          .parents(".language-name")
          .find(".edit-language-name")
          .css("display", "inline");
        button.parents(".language-name-value").text(lang_value);
        if (data != "success") {
          console.log("Error: ", data);
        }
      });
    } else {
      $(this)
        .siblings("input[type=text]")
        .css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
    }
  });

  //Category Actions
  //Add category
  $("#new_category_button").click(function (event) {
    event.preventDefault();

    let category_name_input = $("#new_category_name");
    let category_name = category_name_input.val();

    let validation = true;
    if (category_name.length < 1) {
      category_name_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }
    if (validation) {
      category_name_input.css("border-color", "#ced4da");
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: { sign: "add_category", category_name: category_name },
      }).done(function (data) {
        if (data == "success") {
          window.location.reload();
        } else {
          alert(data);
        }
      });
    }
  });
  //Add Report
  $("#new_report_button").click(function (event) {
    event.preventDefault();

    let new_report_name_input = $("#new_report_name");
    let new_report_name = new_report_name_input.val();

    let validation = true;
    if (new_report_name.length < 1) {
      new_report_name_input.css(
        "border-bottom",
        "3px solid rgba(160, 0, 0, .5)"
      );
      validation = validation & false;
    }
    if (validation) {
      new_report_name_input.css("border-color", "#ced4da");
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: { sign: "add_report", new_report_name: new_report_name },
      }).done(function (data) {
        if (data == "success") {
          window.location.reload();
        } else {
          alert(data);
        }
      });
    }
  });
  //Category Details, TinyMCE init
  var MCEConfig = {
    selector: ".category-details",
    height: 400,
    content_style:
      "@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');",
    font_formats:
      "Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Montserrat=Montserrat, sans-serif;Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats;",
    plugins: [
      "advlist anchor autolink codesample fullscreen help image imagetools",
      " lists link media noneditable preview",
      " searchreplace table template visualblocks wordcount",
    ],
    toolbar:
      "insertfile a11ycheck undo redo | bold italic | forecolor backcolor | codesample | alignleft aligncenter alignright alignjustify | bullist numlist | link image",
    spellchecker_dialog: true,
  };
  tinymce.init(MCEConfig);
  //Category delete
  $(".category_delete").click(function (event) {
    event.preventDefault();
    let category_id = $(this).data("category_id");

    let delete_confirm = confirm($("#translation").data("user_js_phrase38"));
    if (delete_confirm) {
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: { sign: "delete_category", category_id: category_id },
      }).done(function (data) {
        if (data == "success") {
          window.location.reload();
        } else alert(data);
      });
    }
  });
  //Category translation update
  $(".category-translation-save").click(function () {
    let lang_code = $(this).data("lang_code");
    let category_id = $(this).data("category_id");
    let category_name = $(this)
      .parents(".category-single-ctn")
      .find(".category-name")
      .val();
    let category_details =
      tinymce.editors[
        $(this)
          .parents(".category-single-ctn")
          .find(".category-details")
          .attr("id")
      ].getContent();

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "update_translation_category",
        lang_code: lang_code,
        category_id: category_id,
        category_name: category_name,
        category_details: category_details,
      },
    }).done(function (data) {
      if (data == "success") window.location.reload();
      else alert(data);
    });
  });

  //Report delete
  $(".report_delete").click(function (event) {
    event.preventDefault();
    let report_format_id = $(this).data("report_format_id");

    let delete_confirm = confirm(
      "Selected Report format will be deleted! Please update all data associated with this format."
    );
    if (delete_confirm) {
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "delete_report_format",
          report_format_id: report_format_id,
        },
      }).done(function (data) {
        if (data == "success") {
          window.location.reload();
        } else alert(data);
      });
    }
  });

  $(".report_image_upload").change(function () {
    let report_format_id = $(this).data("report_format_id");
    let lang_code = $(this).data("lang_code");
    let logo_data = $(this).prop("files")[0];
    let form_data = new FormData();
    form_data.append("logo", logo_data);
    form_data.append("sign", "update_report_logo");
    form_data.append("report_format_id", report_format_id);
    form_data.append("lang_code", lang_code);

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      dataType: "json",
      cache: false,
      contentType: false,
      processData: false,
      data: form_data,
    }).done(function (data) {
      if (data["success"] == "success") {
        //window.location.reload();
        $("#upload_image_" + lang_code).val(data["img"]);
      } else {
        alert(data);
      }
    });
  });

  //Report translation update
  $(".reportformat-translation-save").click(function () {
    let lang_code = $(this).data("lang_code");
    let report_format_id = $(this).data("report_format_id");
    let report_title = $(this)
      .parents(".category-single-ctn")
      .find(".report-name")
      .val();
    let image_name = $(this)
      .parents(".category-single-ctn")
      .find(".uploaded_image")
      .val();
    let report_desc =
      tinymce.editors[
        $(this)
          .parents(".category-single-ctn")
          .find(".category-details")
          .attr("id")
      ].getContent();

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "update_translation_report",
        lang_code: lang_code,
        report_format_id: report_format_id,
        image_name: image_name,
        report_title: report_title,
        report_desc: report_desc,
      },
    }).done(function (data) {
      if (data == "success") window.location.reload();
      else alert(data);
    });
  });
  //Sortable Category
  $("#category_card_ctn").sortable({
    items: ".category-card",
    update: function (event, ui) {
      let data = [];
      $("#category_card_ctn .category-card").each(function () {
        data.push($(this).attr("id").substring(4));
      });
      let categories = JSON.stringify(data);
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: { sign: "update_category_rank", categories: categories },
      }).done(function (data) {
        console.log(data);
      });
    },
  });
  $("#category_card_ctn").disableSelection();

  //Report Format Actions
  //Add
  $("#new_report_format_button").click(function (event) {
    event.preventDefault();

    let report_format_name_input = $("#new_report_format_name");
    let report_format_name = report_format_name_input.val();
    let report_format_description_input = $("#new_report_format_description");
    let report_format_description = report_format_description_input.val();

    let validation = true;
    if (report_format_name.length < 1) {
      report_format_name_input.css(
        "border-bottom",
        "3px solid rgba(160, 0, 0, .5)"
      );
      validation = validation & false;
    }
    if (report_format_description.length < 1) {
      report_format_description_input.css(
        "border-bottom",
        "3px solid rgba(160, 0, 0, .5)"
      );
      validation = validation & false;
    }

    if (validation) {
      report_format_name_input.css("border-color", "#ced4da");
      report_format_description_input.css("border-color", "#ced4da");
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "add_report_format",
          report_format_name: report_format_name,
          report_format_description: report_format_description,
        },
      }).done(function (data) {
        if (data == "success") {
          window.location.reload();
        } else {
          console.log(data);
          alert(data);
        }
      });
    }
  });

  $("#new_mreport_format_button").click(function (event) {
    event.preventDefault();

    let report_format_name_input = $("#new_report_format_name");
    let mreport_lang = $("#mreport_lang");
    let report_format_name = report_format_name_input.val();
    let report_format_description_input = $("#new_report_format_description");
    let report_format_description = report_format_description_input.val();

    report_format_name_input.css("border-color", "#ced4da");
    report_format_description_input.css("border-color", "#ced4da");
    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "add_mreport_format",
        report_format_name: report_format_name,
        report_format_description: report_format_description,
        report_lang: mreport_lang,
      },
    }).done(function (data) {
      if (data == "success") {
        window.location.reload();
      } else {
        console.log(data);
        alert(data);
      }
    });
  });

  //Industry Actions
  //Add industry
  $("#new_industry_button").click(function (event) {
    event.preventDefault();

    let industry_name_input = $("#new_industry_name");
    let industry_name = industry_name_input.val();

    let validation = true;
    if (industry_name.length < 1) {
      industry_name_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }
    if (validation) {
      industry_name_input.css("border-color", "#ced4da");
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: { sign: "add_industry", industry_name: industry_name },
      }).done(function (data) {
        if (data == "success") {
          window.location.reload();
        } else {
          alert(data);
        }
      });
    }
  });
  //Industry Details, TinyMCE init
  var MCEConfig = {
    selector: ".industry-details",
    height: 400,
    content_style:
      "@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');",
    font_formats:
      "Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Montserrat=Montserrat, sans-serif;Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats;",
    plugins: [
      "advlist anchor autolink codesample fullscreen help image imagetools",
      " lists link media noneditable preview",
      " searchreplace table template visualblocks wordcount",
    ],
    toolbar:
      "insertfile a11ycheck undo redo | bold italic | forecolor backcolor | codesample | alignleft aligncenter alignright alignjustify | bullist numlist | link image",
    spellchecker_dialog: true,
  };
  tinymce.init(MCEConfig);
  //Industry delete
  $(".industry_delete").click(function (event) {
    event.preventDefault();
    let industry_id = $(this).data("industry_id");

    let delete_confirm = confirm($("#translation").data("user_js_phrase36"));
    if (delete_confirm) {
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: { sign: "delete_industry", industry_id: industry_id },
      }).done(function (data) {
        if (data == "success") {
          window.location.reload();
        } else alert(data);
      });
    }
  });

  //response delete
  $(".response_delete").click(function (event) {
    event.preventDefault();
    let responder_id = $(this).data("responder_id");

    let delete_confirm = confirm($("#translation").data("user_js_phrase47"));
    if (delete_confirm) {
      //alert(responder_ticket_data_id);
      console.log(site_url);
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: { sign: "delete_response", responder_id: responder_id },
      }).done(function (data) {
        console.log(data);
        if (data == "success") {
          window.location.reload();
        } else alert(data);
      });
    }
  });

  //Industry translation update
  $(".industry-translation-save").click(function () {
    let lang_code = $(this).data("lang_code");
    let industry_id = $(this).data("industry_id");
    let industry_name = $(this)
      .parents(".industry-single-ctn")
      .find(".industry-name")
      .val();
    let industry_details =
      tinymce.editors[
        $(this)
          .parents(".industry-single-ctn")
          .find(".industry-details")
          .attr("id")
      ].getContent();

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "update_translation_industry",
        lang_code: lang_code,
        industry_id: industry_id,
        industry_name: industry_name,
        industry_details: industry_details,
      },
    }).done(function (data) {
      if (data == "success") window.location.reload();
      else alert(data);
    });
  });

  //Method Actions
  //Add method
  $("#new_method_button").click(function (event) {
    event.preventDefault();

    let method_name_input = $("#new_method_name");
    let method_name = method_name_input.val();
    let method_view = $("#method_view").val();

    let validation = true;
    if (method_name.length < 1) {
      method_name_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }
    if (validation) {
      method_name_input.css("border-color", "#ced4da");
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "add_method",
          method_name: method_name,
          company_id: method_view,
        },
      }).done(function (data) {
        if (data == "success") {
          window.location.reload();
        } else {
          alert(data);
        }
      });
    }
  });
  //Method view update
  $("#method_view").change(function () {
    let company_id = $(this).val();

    $("#method_card_ctn").html('<i class="fas fa-spinner fa-spin"></i>');

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: { sign: "get_methods", company_id: company_id },
    }).done(function (data) {
      let methods = JSON.parse(data);
      let html = "";
      for (let x = 0; x < methods.length; x++) {
        let card = `
                <div class="method-card text-gray-700 ${
                  methods[x]["method_company_id"] == company_id
                    ? "method-highlight"
                    : ""
                }">
                    <label class="method-title text-gray-500">${$(
                      "#translation"
                    ).data("user_js_phrase9")} ${
          methods[x]["method_id"]
        }: </label>
                    ${methods[x]["method_name"]}
                    <a href=site_url + "/user/index.php?route=methods&id=${
                      methods[x]["method_id"]
                    }" class="btn btn-primary btn-sm method-card-btn">
                        <i class="fas fa-edit"></i> ${$("#translation").data(
                          "user_js_phrase10"
                        )}
                    </a>
                </div>
                `;
        html += card;
      }
      $("#method_card_ctn").html(html);
    });
  });
  //Method Details, TinyMCE init
  var MCEConfig = {
    selector: ".method-details",
    height: 400,
    content_style:
      "@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');",
    font_formats:
      "Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Montserrat=Montserrat, sans-serif;Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats;",
    plugins: [
      "advlist anchor autolink codesample fullscreen help image imagetools",
      " lists link media noneditable preview",
      " searchreplace table template visualblocks wordcount",
    ],
    toolbar:
      "insertfile a11ycheck undo redo | bold italic | forecolor backcolor | codesample | alignleft aligncenter alignright alignjustify | bullist numlist | link image",
    spellchecker_dialog: true,
  };
  tinymce.init(MCEConfig);
  //Method basic update
  $("#method_basic").click(function (event) {
    event.preventDefault();
    let method_id = $(this).data("method_id");
    let company_id = $("#method_for").val();
    let restriction = $("#company_restriction").val();
    let method_color = $("#method_color").val();

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "update_basic_method",
        method_id: method_id,
        company_id: company_id,
        restriction: restriction,
        method_color: method_color,
      },
    }).done(function (data) {
      if (data == "success") window.location.reload();
      else alert(data);
    });
  });
  //Method delete
  $("#method_delete").click(function (evnet) {
    event.preventDefault();
    let method_id = $(this).data("method_id");

    let delete_confirm = confirm($("#translation").data("user_js_phrase11"));
    if (delete_confirm) {
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: { sign: "delete_method", method_id: method_id },
      }).done(function (data) {
        if (data == "success") {
          window.location.href = "/user/index.php?route=methods";
        } else alert(data);
      });
    }
  });
  //Method translation update
  $(".method-translation-save").click(function () {
    let lang_code = $(this).data("lang_code");
    let method_id = $(this).data("method_id");
    let method_name = $(this)
      .parents(".method-single-ctn")
      .find(".method-name")
      .val();
    let method_details =
      tinymce.editors[
        $(this).parents(".method-single-ctn").find(".method-details").attr("id")
      ].getContent();

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "update_translation_method",
        lang_code: lang_code,
        method_id: method_id,
        method_name: method_name,
        method_details: method_details,
      },
    }).done(function (data) {
      if (data == "success") window.location.reload();
      else alert(data);
    });
  });

  //Question Actions
  //Add question
  $("#new_question_button").click(function (event) {
    event.preventDefault();

    let question_name_input = $("#new_question_name");
    let question_name = question_name_input.val();

    let validation = true;
    if (question_name.length < 1) {
      question_name_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }
    if (validation) {
      question_name_input.css("border-color", "#ced4da");
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: { sign: "add_question", question_name: question_name },
      }).done(function (data) {
        if (data == "success") {
          window.location.reload();
        } else {
          alert(data);
        }
      });
    }
  });
  //Question view update
  $("#question_view").change(function () {
    let company_id = $(this).val();
    if (company_id.length > 0) {
      window.location.href =
        site_url + "/user/index.php?route=questions&company=" + company_id;
    } else {
      window.location.href = site_url + "/user/index.php?route=questions";
    }
  });
  //Question settings
  $("#question_type").change(function (event) {
    let question_type = $(this).val();
    let question_id = $(this).data("question_id");
    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "update_question_type",
        question_id: question_id,
        question_type: question_type,
      },
    }).done(function (data) {
      if (data == "success") {
        window.location.reload();
      } else {
        alert(data);
      }
    });
  });
  $("#question_follow_up").change(function (event) {
    let question_follow_up = parseInt($(this).val());
    let question_id = $(this).data("question_id");
    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "update_question_follow_up",
        question_id: question_id,
        question_follow_up: question_follow_up,
      },
    }).done(function (data) {
      if (data == "success") {
        window.location.reload();
      } else {
        alert(data);
      }
    });
  });

  //Question tips, TinyMCE init
  var MCEConfig = {
    selector: ".question-tips-yes, .question-tips-no",
    height: 300,
    content_style:
      "@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');",
    font_formats:
      "Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Montserrat=Montserrat, sans-serif;Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats;",
    plugins: [
      "advlist anchor autolink codesample fullscreen help image imagetools",
      " lists link media noneditable preview",
      " searchreplace table template visualblocks wordcount",
    ],
    toolbar:
      "insertfile a11ycheck undo redo | bold italic | forecolor backcolor | codesample | alignleft aligncenter alignright alignjustify | bullist numlist | link image",

    spellchecker_dialog: true,
  };
  tinymce.init(MCEConfig);
  //Question basic update
  $("#question_basic").click(function (event) {
    event.preventDefault();
    let question_id = $(this).data("question_id");
    let yes_response = $("#question_methods_yes").val().toString();
    let no_response = $("#question_methods_no").val().toString();
    let question_activate_tip_yes = $("#question_activate_tip_yes").is(
      ":checked"
    )
      ? 1
      : 0;
    let question_activate_tip_no = $("#question_activate_tip_no").is(":checked")
      ? 1
      : 0;
    let company_id = $(this).data("company_id");
    let question_catetory = "";
    let question_follow_up_yes = 0;
    let question_follow_up_no = 0;
    let question_weight_yes = $("#question_weight_yes").val();
    let question_weight_no = $("#question_weight_no").val();

    if ($("#question_category").length)
      question_catetory = $("#question_category").val();
    if ($("#question_follow_up_yes").length)
      question_follow_up_yes = $("#question_follow_up_yes").val();
    if ($("#question_follow_up_no").length)
      question_follow_up_no = $("#question_follow_up_no").val();

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "update_question_basic",
        question_id: question_id,
        category_id: question_catetory,
        yes_response: yes_response,
        no_response: no_response,
        question_activate_tip_yes: question_activate_tip_yes,
        question_activate_tip_no: question_activate_tip_no,
        question_follow_up_yes: question_follow_up_yes,
        question_follow_up_no: question_follow_up_no,
        question_weight_yes: question_weight_yes,
        question_weight_no: question_weight_no,
        company_id: company_id,
      },
    }).done(function (data) {
      if (data == "success") window.location.reload();
      else alert(data);
    });
  });
  //Question delete
  $("#question_delete").click(function (evnet) {
    event.preventDefault();
    let question_id = $(this).data("question_id");

    let delete_confirm = confirm($("#translation").data("user_js_phrase12"));
    if (delete_confirm) {
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: { sign: "delete_question", question_id: question_id },
      }).done(function (data) {
        if (data == "success") {
          window.location.href = "/user/index.php?route=questions";
        } else alert(data);
      });
    }
  });
  //question translation update
  $(".question-translation-save").click(function () {
    let lang_code = $(this).data("lang_code");
    let question_id = $(this).data("question_id");
    let question_name = $(this)
      .parents(".question-single-ctn")
      .find(".question-name")
      .val();
    let question_tips_yes =
      tinymce.editors[
        $(this)
          .parents(".question-single-ctn")
          .find(".question-tips-yes")
          .attr("id")
      ].getContent();
    let question_tips_no =
      tinymce.editors[
        $(this)
          .parents(".question-single-ctn")
          .find(".question-tips-no")
          .attr("id")
      ].getContent();
    let question_option1 = "";
    let question_option2 = "";
    let question_option3 = "";
    let question_option4 = "";
    let question_option5 = "";
    let question_option6 = "";

    let question_option1_input = $(this)
      .parents(".question-single-ctn")
      .find(".question_option1");
    let question_option2_input = $(this)
      .parents(".question-single-ctn")
      .find(".question_option2");
    let question_option3_input = $(this)
      .parents(".question-single-ctn")
      .find(".question_option3");
    let question_option4_input = $(this)
      .parents(".question-single-ctn")
      .find(".question_option4");
    let question_option5_input = $(this)
      .parents(".question-single-ctn")
      .find(".question_option5");
    let question_option6_input = $(this)
      .parents(".question-single-ctn")
      .find(".question_option6");

    if (question_option1_input.length)
      question_option1 = question_option1_input.val();
    if (question_option2_input.length)
      question_option2 = question_option2_input.val();
    if (question_option3_input.length)
      question_option3 = question_option3_input.val();
    if (question_option4_input.length)
      question_option4 = question_option4_input.val();
    if (question_option5_input.length)
      question_option5 = question_option5_input.val();
    if (question_option6_input.length)
      question_option6 = question_option6_input.val();

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "update_translation_question",
        lang_code: lang_code,
        question_id: question_id,
        question_name: question_name,
        question_tips_yes: question_tips_yes,
        question_tips_no: question_tips_no,
        question_option1: question_option1,
        question_option2: question_option2,
        question_option3: question_option3,
        question_option4: question_option4,
        question_option5: question_option5,
        question_option6: question_option6,
      },
    }).done(function (data) {
      if (data == "success") window.location.reload();
      else alert(data);
    });
  });

  //Ticket Actions
  //Summarize ticket, TinyMCE init

  var MCEConfig = {
    selector: "#ticket_summary, #ticket_review, #rating_text_1, #rating_text_2",
    height: 300,
    content_style:
      "@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');",
    font_formats:
      "Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Montserrat=Montserrat, sans-serif;Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats;",
    plugins: [
      "advlist anchor autolink codesample fullscreen help image imagetools",
      " lists link media noneditable preview",
      " searchreplace table template visualblocks wordcount",
    ],
    toolbar:
      "insertfile a11ycheck undo redo | bold italic | forecolor backcolor | codesample | alignleft aligncenter alignright alignjustify | bullist numlist | link image",
    spellchecker_dialog: true,
  };
  tinymce.init(MCEConfig);

  //Create ticket
  $("#create_ticket").click(function (event) {
    event.preventDefault();

    let ticket_name_input = $("#ticket_name");
    let ticket_name = ticket_name_input.val();
    let ticket_summary = tinymce.editors["ticket_summary"].getContent();

    let _reqId = $("#_reqId").val();

    let selectUserTicket = "";
    let selectTicketType = "";
    let selectCompanyTicket = "";
    selectTicketType = $(
      "#selectTicketTypeRelation option:not(:first-child):selected"
    ).val();
    selectUserTicket = $(
      "#selectUserTicket option:not(:first-child):selected"
    ).val();
    selectCompanyTicket = $(
      "#selectCompanyTicket option:not(:first-child):selected"
    ).val();

    let validation = true;
    if (ticket_name.length < 1) {
      validation = validation & false;
      ticket_name_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      window.scrollTo(0, 0);
    } else if (_reqId.length < 1) {
      alert("Invalid Request ID");
    } else {
      ticket_name_input.css("border-color", "#ced4da");

      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "create_ticket",
          ticket_name: ticket_name,
          ticketType: selectTicketType,
          userTicket: selectUserTicket,
          companyTicket: selectCompanyTicket,
          ticket_summary: ticket_summary,
          _reqId: _reqId,
        },
      }).done(function (data) {
        data = JSON.parse(data);
        if (data["status"] == "success") {
          window.location.href =
            site_url +
            "/user/index.php?route=ticket&id=" +
            data["ticket_id"] +
            "&page=intro";
        } else {
          alert(data["message"]);
        }
      });
    }
  });

  //update ticket summary
  $("#update_ticket_summary").click(function (event) {
    event.preventDefault();

    let ticket_id = $(this).data("ticket_id");
    let ticket_name = $("#ticket_name").val();
    let ticket_summary = tinymce.editors["ticket_summary"].getContent();

    let validation = true;
    if (ticket_name.length < 1) {
      validation = validation & false;
      $("#ticket_name").css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      window.scrollTo(0, 0);
    } else {
      $("#ticket_name").css("border-color", "#ced4da");

      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "update_ticket_summary",
          ticket_id: ticket_id,
          ticket_name: ticket_name,
          ticket_summary: ticket_summary,
        },
      }).done(function (data) {
        data = JSON.parse(data);
        if (data["status"] == "success") {
          window.location.href =
            site_url +
            "/user/index.php?route=ticket&id=" +
            ticket_id +
            "&page=question";
        } else {
          alert(data["message"]);
        }
      });
    }
  });

  //Question table
  $(".question-table tbody tr").hover(
    function () {
      let question_id = $(this).attr("id").substring(9);
      let yes_check = $(this).find(".yes-check");
      let no_check = $(this).find(".no-check");
      let mcq_check = $(this).find(".mcq-check:checked");
      if (yes_check.is(":checked")) {
        if (yes_check.data("tip_enabled")) {
          let html = `
                    <div class="question-tip">
                        <i class="fas fa-caret-down question-tip-arrow"></i>
                        <i class="fas fa-lightbulb tip-bulb"></i> ${$(
                          "#translation"
                        ).data("user_js_phrase13")}
                    </div>
                `;
          yes_check.parents("td").append(html);
        }
      }
      if (no_check.is(":checked")) {
        if (no_check.data("tip_enabled")) {
          let html = `
                    <div class="question-tip">
                        <i class="fas fa-caret-down question-tip-arrow"></i>
                        <i class="fas fa-lightbulb tip-bulb"></i> ${$(
                          "#translation"
                        ).data("user_js_phrase13")}
                    </div>
                `;
          no_check.parents("td").append(html);
        }
      }
      if (mcq_check.length) {
        if (mcq_check.data("tip_enabled")) {
          let tips_type = mcq_check.data("tip");
          if (tips_type == "yes") {
            mcq_check
              .parents("td")
              .find(".tip-view.yes-tip")
              .css("display", "block");
            mcq_check
              .parents("td")
              .find(".tip-view.no-tip")
              .css("display", "none");
          } else if (tips_type == "no") {
            mcq_check
              .parents("td")
              .find(".tip-view.yes-tip")
              .css("display", "none");
            mcq_check
              .parents("td")
              .find(".tip-view.no-tip")
              .css("display", "block");
          } else {
            mcq_check
              .parents("td")
              .find(".tip-view.yes-tip")
              .css("display", "block");
            mcq_check
              .parents("td")
              .find(".tip-view.no-tip")
              .css("display", "block");
          }
          let html = `
                    <div class="question-tip">
                        <i class="fas fa-caret-down question-tip-arrow"></i>
                        <i class="fas fa-lightbulb tip-bulb"></i> ${$(
                          "#translation"
                        ).data("user_js_phrase13")}
                    </div>
                `;
          mcq_check.parents("td").append(html);
        }
      }
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
      if (class_name == "yes-check" && $(this).data("tip_enabled")) {
        let html = `
                    <div class="question-tip">
                        <i class="fas fa-caret-down question-tip-arrow"></i>
                        <i class="fas fa-lightbulb tip-bulb"></i> ${$(
                          "#translation"
                        ).data("user_js_phrase13")}
                    </div>
                `;
        $(this).parents("td").append(html);
      }
      if (class_name == "no-check" && $(this).data("tip_enabled")) {
        let html = `
                    <div class="question-tip">
                        <i class="fas fa-caret-down question-tip-arrow"></i>
                        <i class="fas fa-lightbulb tip-bulb"></i> ${$(
                          "#translation"
                        ).data("user_js_phrase13")}
                    </div>
                `;
        $(this).parents("td").append(html);
      }
      if (class_name == "mcq-check" && $(this).data("tip_enabled")) {
        let tips_type = $(this).data("tip");
        if (tips_type == "yes") {
          $(this)
            .parents("td")
            .find(".tip-view.yes-tip")
            .css("display", "block");
          $(this).parents("td").find(".tip-view.no-tip").css("display", "none");
        } else if (tips_type == "no") {
          $(this)
            .parents("td")
            .find(".tip-view.yes-tip")
            .css("display", "none");
          $(this)
            .parents("td")
            .find(".tip-view.no-tip")
            .css("display", "block");
        } else {
          $(this)
            .parents("td")
            .find(".tip-view.yes-tip")
            .css("display", "block");
          $(this)
            .parents("td")
            .find(".tip-view.no-tip")
            .css("display", "block");
        }
        let html = `
                    <div class="question-tip">
                        <i class="fas fa-caret-down question-tip-arrow"></i>
                        <i class="fas fa-lightbulb tip-bulb"></i> ${$(
                          "#translation"
                        ).data("user_js_phrase13")}
                    </div>
                `;
        $(this).parents("td").append(html);
      }
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

    //Uncheck other checkbox but current one.
    $(this)
      .parents("tr")
      .find("input[type=radio]")
      .not(this)
      .prop("checked", false);

    //Update method dynamically
    let response = {};
    let response_count = 0;
    $(".question-row").each(function () {
      let question_id = $(this).data("question_id");
      let answer;
      let q_type = $(this).data("question_type");
      let q_follow_up = $(this).data("question_follow_up");
      let q_yes_follow_up = $(this).data("question_yes_follow_up");
      let q_no_follow_up = $(this).data("question_no_follow_up");
      if ($(this).data("question_type") == "mcq") {
        if ($(this).find(".check_1").is(":checked")) {
          answer = 1;
          response_count++;
        } else if ($(this).find(".check_2").is(":checked")) {
          answer = 2;
          response_count++;
        } else if ($(this).find(".check_3").is(":checked")) {
          answer = 3;
          response_count++;
        } else if ($(this).find(".check_4").is(":checked")) {
          answer = 4;
          response_count++;
        } else if ($(this).find(".check_5").is(":checked")) {
          answer = 5;
          response_count++;
        } else answer = 0;
      } else if ($(this).data("question_type") == "yes-no") {
        if ($(this).find(".yes-check").is(":checked")) {
          answer = 2;
          response_count++;
        } else if ($(this).find(".no-check").is(":checked")) {
          answer = 1;
          response_count++;
        } else answer = 0;
      }

      response[question_id] = {
        answer: answer,
        type: q_type,
        "follow-up": q_follow_up,
        "yes-follow-up": q_yes_follow_up,
        "no-follow-up": q_no_follow_up,
      };
    });

    if (response_count > 20) {
      response = JSON.stringify(response);
      $(".ticket-method-card").remove();
      $(".ticket-method-ctn").append('<i class="fas fa-spinner fa-spin"></i>');
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "generate_dynamic_method",
          response: response,
        },
      }).done(function (data) {
        let methods = JSON.parse(data);
        if (methods) {
          let html = "";
          for (let key in methods) {
            html += `
                        <div class="ticket-method-card">
                            <div class="row">
                                <div class="col-2 method-card-number">
                                    ${parseInt(key) + 1}
                                </div>
                                <div class="col-7 method-card-title">
                                    ${methods[key]["method_name"]}
                                </div>
                                <div class="col-3 method-card-readmore">
                                    <button class="btn btn-light btn-sm method-card-btn ticket-method-card-readmore"><i class="fas fa-chevron-down"></i></button>
                                    <button class="btn btn-dark btn-sm method-card-btn method-percent-btn mr-1">${
                                      methods[key]["method_percentage"]
                                    }%</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 method-card-details ticket-method-card-details">
                                ${methods[key]["method_details"]}
                                </div>
                            </div>
                        </div>
                        `;
          }
          $(".ticket-method-ctn").find("i").remove();
          $(".ticket-method-ctn").append(html);
        } else {
          $(".ticket-method-ctn").find("i").remove();
        }
      });
    } else {
      $(".ticket-method-block").html("");
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

    console.log(selectedOption.data("tip"));

    console.log("follow_up_type :" + follow_up_type);

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

    //Uncheck other checkbox but current one.
    $(this)
      .parents("tr")
      .find("input[type=radio]")
      .not(this)
      .prop("checked", false);

    //Update method dynamically
    let response = {};
    let response_count = 0;
    $(".question-row").each(function () {
      let question_id = $(this).data("question_id");
      let answer;
      let q_type = $(this).data("question_type");
      let q_follow_up = $(this).data("question_follow_up");
      let q_yes_follow_up = $(this).data("question_yes_follow_up");
      let q_no_follow_up = $(this).data("question_no_follow_up");
      if ($(this).data("question_type") == "mcq") {
        if ($(this).find(".check_1").is(":checked")) {
          answer = 1;
          response_count++;
        } else if ($(this).find(".check_2").is(":checked")) {
          answer = 2;
          response_count++;
        } else if ($(this).find(".check_3").is(":checked")) {
          answer = 3;
          response_count++;
        } else if ($(this).find(".check_4").is(":checked")) {
          answer = 4;
          response_count++;
        } else if ($(this).find(".check_5").is(":checked")) {
          answer = 5;
          response_count++;
        } else answer = 0;
      } else if ($(this).data("question_type") == "yes-no") {
        if ($(this).find(".yes-check").is(":checked")) {
          answer = 2;
          response_count++;
        } else if ($(this).find(".no-check").is(":checked")) {
          answer = 1;
          response_count++;
        } else answer = 0;
      }

      response[question_id] = {
        answer: answer,
        type: q_type,
        "follow-up": q_follow_up,
        "yes-follow-up": q_yes_follow_up,
        "no-follow-up": q_no_follow_up,
      };
    });

    if (response_count > 20) {
      response = JSON.stringify(response);
      $(".ticket-method-card").remove();
      $(".ticket-method-ctn").append('<i class="fas fa-spinner fa-spin"></i>');
      $.ajax({
        url: "/option_server.php",
        type: "POST",
        data: {
          sign: "generate_dynamic_method",
          response: response,
        },
      }).done(function (data) {
        let methods = JSON.parse(data);
        if (methods) {
          let html = "";
          for (let key in methods) {
            html += `
                        <div class="ticket-method-card">
                            <div class="row">
                                <div class="col-2 method-card-number">
                                    ${parseInt(key) + 1}
                                </div>
                                <div class="col-7 method-card-title">
                                    ${methods[key]["method_name"]}
                                </div>
                                <div class="col-3 method-card-readmore">
                                    <button class="btn btn-light btn-sm method-card-btn ticket-method-card-readmore"><i class="fas fa-chevron-down"></i></button>
                                    <button class="btn btn-dark btn-sm method-card-btn method-percent-btn mr-1">${
                                      methods[key]["method_percentage"]
                                    }%</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 method-card-details ticket-method-card-details">
                                ${methods[key]["method_details"]}
                                </div>
                            </div>
                        </div>
                        `;
          }
          $(".ticket-method-ctn").find("i").remove();
          $(".ticket-method-ctn").append(html);
        } else {
          $(".ticket-method-ctn").find("i").remove();
        }
      });
    } else {
      $(".ticket-method-block").html("");
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

  //Close Tips
  $(".tip-view").click(function () {
    $(this).parent(".tip-view-ctn").fadeOut(200);
  });

  //Save ticket
  $("#save_ticket").click(function (event) {
    //alert('test');
    event.preventDefault();
    //alert('teste');

    let ticket_name_input = $("#ticket_name");
    let ticket_name = ticket_name_input.val();
    let response = {};
    let ticket_id = $(this).data("ticket_id");

    let validation = true;
    if (ticket_name.length < 1) {
      validation = validation & false;
      ticket_name_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      window.scrollTo(0, 0);
    } else {
      ticket_name_input.css("border-color", "#ced4da");
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

      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "save_ticket",
          ticket_name: ticket_name,
          response: response,
          ticket_id: ticket_id,
        },
      }).done(function (data) {
        console.log(data);
        data = JSON.parse(data);
        if (data["status"] == "success") {
          window.location.href =
            site_url +
            "/user/index.php?route=ticket&id=" +
            data["ticket_id"] +
            "&page=question";
        } else {
          alert(data["message"]);
        }
      });
    }
  });

  //Close ticket
  $("#close_ticket").click(function (event) {
    event.preventDefault();
    let ticket_id = $(this).data("ticket_id");

    let submit_confirmation = confirm(
      $("#translation").data("user_js_phrase15")
    );

    if (submit_confirmation) {
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "close_ticket",
          ticket_id: ticket_id,
        },
      }).done(function (data) {
        data = JSON.parse(data);
        if (data["status"] == "success") {
          window.location.href = site_url + "/user/index.php?route=tickets";
        } else {
          alert(data["message"]);
        }
      });
    }
  });

  //Submit ticket
  $("#submit_ticket").click(function (event) {
    event.preventDefault();

    let ticket_name_input = $("#ticket_name");
    let ticket_name = ticket_name_input.val();
    let response = {};
    let ticket_id = $(this).data("ticket_id");
    let validation = true;

    $(".question-row").each(function () {
      let question_id = $(this).data("question_id");
      let answer;
      let q_type = $(this).data("question_type");
      let q_follow_up = $(this).data("question_follow_up");
      let q_yes_follow_up = $(this).data("question_yes_follow_up");
      let q_no_follow_up = $(this).data("question_no_follow_up");
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
      };
    });

    let totalAnswered = [];

    $.each(response, function (key, value) {
      totalAnswered.push(value.answer);
    });

    // console.log(totalAnswered.length);

    response = JSON.stringify(response);

    var dataArray = [];
    $(".totalAnswerInResponder").each(function () {
      var dataValue = $(this).data("totalsubmission");
      if (dataValue < 51) {
        dataArray.push(0);
      } else {
        dataArray.push(1);
      }
    });

    let respondentPending = false;

    if (dataArray.indexOf(0) !== -1) {
      respondentPending = true;
    } else {
      respondentPending = false;
    }

    if (ticket_name.length < 1) {
      validation = validation & false;
      ticket_name_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      window.scrollTo(0, 0);
    }

    if (respondentPending) {
      if (confirm($("#translation").data("user_js_phrase46"))) {
        validationAppoved = validation;
      } else {
        validationAppoved = false;
      }
    } else {
      validationAppoved = validation;
    }

    if (validationAppoved) {
      ticket_name_input.css("border-color", "#ced4da");

      let submit_confirmation = confirm(
        $("#translation").data("user_js_phrase15")
      );

      if (submit_confirmation) {
        $.ajax({
          url: site_url + "/option_server.php",
          type: "POST",
          data: {
            sign: "submit_report",
            ticket_name: ticket_name,
            response: response,
            ticket_id: ticket_id,
          },
        }).done(function (data) {
          data = JSON.parse(data);
          if (data["status"] == "success") {
            window.location.href =
              site_url +
              "/user/index.php?route=ticket&id=" +
              data["report_id"] +
              "&page=review";
          } else {
            alert(data["message"]);
          }
        });
      }
    }
  });
  //Review questions
  $("#review_submit").click(function (event) {
    event.preventDefault();
    let ticket_id = $(this).data("ticket_id");
    let ticket_review_status = $(this).data("ticket_review_status");
    let review_status = "";
    $(".review-check").each(function () {
      if ($(this).is(":checked")) review_status += $(this).val() + ",";
    });
    if (review_status.length > 1)
      review_status = review_status.substring(0, review_status.length - 1);

    let review_text = tinymce.editors["ticket_review"].getContent();

    const checkbox_is_one = document.querySelectorAll(".review-check");

    // Count how many checkboxes are checked
    const numChecked = Array.from(checkbox_is_one).filter(
      (checkbox) => checkbox.checked
    ).length;

    // Check if at least one checkbox is checked
    if (numChecked === 0) {
      alert("Please select at least one option.");
      event.preventDefault(); // Prevent form submission

      return false;
    }

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "submit_review",
        ticket_id: ticket_id,
        review_status: review_status,
        review_text: review_text,
        ticket_review_status: ticket_review_status,
      },
    }).done(function (data) {
      data = JSON.parse(data);
      if (data["status"] == "success") {
        window.location.href =
          site_url +
          "/user/index.php?route=ticket&id=" +
          ticket_id +
          "&page=rating";
      } else {
        alert(data["message"]);
      }
    });
    console.log(review_status);
  });
  //Ratings
  $(".smiley-check i").click(function () {
    if ($("#rating_status_value").length) {
      if ($("#rating_status_value").val() != "1") {
        $(this).parent().find("i").removeClass("active");
        $(this).addClass("active");
      }
    }
  });
  $("#rating_submit").click(function (event) {
    event.preventDefault();
    let ticket_id = $(this).data("ticket_id");
    let ticket_rating_status = $(this).data("ticket_rating_status");
    let rating_check_1 = 0;
    let rating_check_2 = 0;
    let rating_check_3 = 0;
    let rating_check_4 = 0;

    $("#rating_check_1 i").each(function (index) {
      if ($(this).hasClass("active")) rating_check_1 = index + 1;
    });
    $("#rating_check_2 i").each(function (index) {
      if ($(this).hasClass("active")) rating_check_2 = index + 1;
    });
    $("#rating_check_3 i").each(function (index) {
      if ($(this).hasClass("active")) rating_check_3 = index + 1;
    });
    $("#rating_check_4 i").each(function (index) {
      if ($(this).hasClass("active")) rating_check_4 = index + 1;
    });

    let rating_text_1 = tinymce.editors["rating_text_1"].getContent();
    let rating_text_2 = tinymce.editors["rating_text_2"].getContent();

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "submit_rating",
        ticket_id: ticket_id,
        ticket_rating_status: ticket_rating_status,
        rating_check_1: rating_check_1,
        rating_check_2: rating_check_2,
        rating_check_3: rating_check_3,
        rating_check_4: rating_check_4,
        rating_text_1: rating_text_1,
        rating_text_2: rating_text_2,
      },
    }).done(function (data) {
      data = JSON.parse(data);
      if (data["status"] == "success") {
        window.location.href = site_url + "/user/index.php?route=tickets";
      } else {
        alert(data["message"]);
      }
    });
    console.log(review_status);
  });

  //Report actions
  $(".ticket-method-ctn").on(
    "click",
    ".ticket-method-card-readmore",
    function () {
      let details = $(this)
        .parents(".ticket-method-card")
        .find(".ticket-method-card-details");
      if (details.is(":visible")) {
        $(".ticket-method-card-details").slideUp(200);
        $(".ticket-method-card-readmore")
          .find("i")
          .removeClass("fa-chevron-up")
          .addClass("fa-chevron-down");
        $(this)
          .find("i")
          .removeClass("fa-chevron-up")
          .addClass("fa-chevron-down");
      } else {
        $(".ticket-method-card-details").slideUp(200);
        details.slideDown(200);
        $(".ticket-method-card-readmore")
          .find("i")
          .removeClass("fa-chevron-up")
          .addClass("fa-chevron-down");
        $(this)
          .find("i")
          .removeClass("fa-chevron-down")
          .addClass("fa-chevron-up");
      }
    }
  );

  //Send report to user
  $("#send_report_email").click(function (event) {
    let user_email = $(this).data("user_email");
    let ticket_id = $(this).data("ticket_id");
    let button = $(this);

    button.append(' <i class="fas fa-spin fa-spinner"></i>');
    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "send_report_email",
        user_email: user_email,
        ticket_id: ticket_id,
      },
    }).done(function (data) {
      button.find("i").remove();
      if (data == "success") alert($("#translation").data("user_js_phrase30"));
      else alert(data);
    });
  });

  //Deadline updates for ticket
  $("#user-full-screen-close").click(function () {
    $("#user-full-screen").slideUp(200);
    $("#user-full-screen-content").html("");
  });

  $("#ticket_deadline_update").click(function () {
    $("#user-full-screen").slideDown(200);
    $("#user-full-screen-loader").css("display", "none");
    let ticket_id = $(this).data("ticket_id");
    let ticket_end_date = $(this).data("end_date");
    let ticket_summary = $(this).data("summary");
    let ticket_description = $(this).data("description");

    let html = `
            <div class="ticket-deadline-form">
                <form>
                    <div class="form-group">
                        <label for="ticket_deadline_date"> ${$(
                          "#translation"
                        ).data("user_js_phrase16")} </label>
                        <input type="date" id="ticket_deadline_date" class="form-control" value="${ticket_end_date}">
                    </div> 
                    <div class="form-group">
                        <label for="ticket_deadline_date"> ${$(
                          "#translation"
                        ).data("user_js_phrase17")} </label>
                        <textarea id="ticket_deadline_summary" class="form-control">${ticket_summary}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="ticket_deadline_date"> ${$(
                          "#translation"
                        ).data("user_js_phrase18")} </label>
                        <textarea id="ticket_deadline_description" class="form-control">${ticket_description}</textarea>
                    </div>
                    <button type="submit" id="ticket_deadline_save" class="btn btn-info" data-ticket_id="${ticket_id}">${$(
      "#translation"
    ).data("user_js_phrase19")}</button>
                </form>
            </div>
        `;
    $("#user-full-screen-content").html(html);
    $("#user-full-screen-content").css("display", "block");
  });
  $("#user-full-screen-content").on(
    "click",
    "#ticket_deadline_save",
    function (event) {
      event.preventDefault();
      let deadline_date_input = $("#ticket_deadline_date");
      let deadline_summary_input = $("#ticket_deadline_summary");
      let deadline_description_input = $("#ticket_deadline_description");

      let ticket_id = $(this).data("ticket_id");
      let deadline_date = deadline_date_input.val();
      let deadline_summary = deadline_summary_input.val();
      let deadline_description = deadline_description_input.val();

      let validation = true;
      if (deadline_date.length < 1) {
        validation = validation & false;
        deadline_date_input.css(
          "border-bottom",
          "3px solid rgba(160, 0, 0, .5)"
        );
      }

      if (validation) {
        deadline_date_input.css("border-color", "#ced4da");

        $.ajax({
          url: site_url + "/option_server.php",
          type: "POST",
          data: {
            sign: "ticket_deadline_update",
            ticket_id: ticket_id,
            deadline_date: deadline_date,
            deadline_summary: deadline_summary,
            deadline_description: deadline_description,
          },
        }).done(function (data) {
          if (data == "success") window.location.reload();
          else alert(data);
        });
      }
    }
  );

  //Deadline update for question
  $(".question-deadline-update").click(function () {
    $("#user-full-screen").slideDown(200);
    $("#user-full-screen-loader").css("display", "none");
    let ticket_id = $(this).data("ticket_id");
    let question_id = $(this).data("question_id");
    let question_end_date = $(this).data("end_date");
    let question_summary = $(this).data("summary");
    let question_description = $(this).data("description");

    let html = `
            <div class="question-deadline-form">
                <form>
                    <div class="form-group">
                        <label for="question_deadline_date"> ${$(
                          "#translation"
                        ).data("user_js_phrase16")} </label>
                        <input type="date" id="question_deadline_date" class="form-control" value="${question_end_date}">
                    </div> 
                    <div class="form-group">
                        <label for="question_deadline_date"> ${$(
                          "#translation"
                        ).data("user_js_phrase17")} </label>
                        <textarea id="question_deadline_summary" class="form-control">${question_summary}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="question_deadline_date"> ${$(
                          "#translation"
                        ).data("user_js_phrase18")} </label>
                        <textarea id="question_deadline_description" class="form-control">${question_description}</textarea>
                    </div>
                    <button type="submit" id="question_deadline_save" class="btn btn-info" data-ticket_id="${ticket_id}" data-question_id="${question_id}">${$(
      "#translation"
    ).data("user_js_phrase19")}</button>
                </form>
            </div>
        `;
    $("#user-full-screen-content").html(html);
    $("#user-full-screen-content").css("display", "block");
  });
  $("#user-full-screen-content").on(
    "click",
    "#question_deadline_save",
    function (event) {
      event.preventDefault();
      let deadline_date_input = $("#question_deadline_date");
      let deadline_summary_input = $("#question_deadline_summary");
      let deadline_description_input = $("#question_deadline_description");

      let ticket_id = $(this).data("ticket_id");
      let question_id = $(this).data("question_id");
      let deadline_date = deadline_date_input.val();
      let deadline_summary = deadline_summary_input.val();
      let deadline_description = deadline_description_input.val();

      let validation = true;
      if (deadline_date.length < 1) {
        validation = validation & false;
        deadline_date_input.css(
          "border-bottom",
          "3px solid rgba(160, 0, 0, .5)"
        );
      }

      if (validation) {
        deadline_date_input.css("border-color", "#ced4da");

        $.ajax({
          url: site_url + "/option_server.php",
          type: "POST",
          data: {
            sign: "question_deadline_update",
            ticket_id: ticket_id,
            question_id: question_id,
            deadline_date: deadline_date,
            deadline_summary: deadline_summary,
            deadline_description: deadline_description,
          },
        }).done(function (data) {
          if (data == "success") window.location.reload();
          else alert(data);
        });
      }
    }
  );
  //Calendar event reminder
  $(".calendar_event_reminder").click(function (event) {
    event.preventDefault();

    let ticket_id = $(this).data("ticket_id");
    let end_date = $(this).data("end_date");
    let summary = $(this).data("summary");
    let description = $(this).data("description");

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "create_calender_event",
        ticket_id: ticket_id,
        end_date: end_date,
        summary: summary,
        description: description,
      },
    }).done(function (data) {
      if (data == "success") alert($("#translation").data("user_js_phrase25"));
      else alert(data);
    });
  });
// 
// $('.hover-effect').click(function(){
  
// })


// 
  //Report Composer kawsar
  //TinyMCE init
  var MCEConfig = {
    selector: ".composer-text",
    height: 300,
    content_style:
      "@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');",
    font_formats:
      "Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Montserrat=Montserrat, sans-serif;Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats;",
    plugins: [
      "advlist anchor autolink codesample fullscreen help image imagetools",
      "lists link media noneditable preview",
      "searchreplace table template visualblocks wordcount",
      "print preview pagebreak nonbreaking help importcss ",
    ],
    toolbar:
      "insertfile a11ycheck undo redo | bold italic | forecolor backcolor | codesample | alignleft aligncenter alignright alignjustify | bullist numlist",
    menu: {
      graph: { title: "Graph", items: "addgraphradar addgraphdoughnut addtable addtableCategories addtableCompanyCategories" },
      tips: { title: "Tips", items: "addTips" },
    },
    menubar: "file edit view insert format tools table graph tips help",
    setup: (editor) => {
      editor.ui.registry.addMenuItem("addgraphradar", {
        text: "Add Radar Graph",
        onAction: () => {
          $("#choose_graph_res1").val("1");
          $("#graphModal").modal("show");
        },
      });
      editor.ui.registry.addMenuItem("addgraphdoughnut", {
        text: "Add Doughnut Graph",
        onAction: () => {
          $("#choose_graph_res1").val("2");
          $("#graphModal").modal("show");
        },
      });
      editor.ui.registry.addMenuItem("addtable", {
        text: "Table Questions",
        onAction: () => {
          $("#tableModal").modal("show");
          if ($.isEmptyObject($('.tableContainer').html())) {
            createDataTable();
          }
        },
      });
      editor.ui.registry.addMenuItem("addtableCategories", {
        text: "Table Categories",
        onAction: () => {
          $("#categoriesModal").modal("show");
          if ($.isEmptyObject($('.tableCategoriesContainer').html())) {
            createDataTable();
          }
        },
      });
      editor.ui.registry.addMenuItem("addtableCompanyCategories", {
        text: "Company Categories",
        onAction: () => {
          $("#categoriesCompanyModal").modal("show");
          if ($.isEmptyObject($('.tableCompanyCategoriesContainer').html())) {
            createDataTable();
          }
        },
      });
      editor.ui.registry.addMenuItem("addTips", {
        text: "Add Tips",
        onAction: () => {

         
        
         
          $('#ques_tips').modal('show');
          $.ajax({
            url: site_url + "/ajax.php",
            type: 'POST',
            dataType: 'JSON',
            data: {
              currLang: $('#footer_language_selector').val(),
            },
            success: function (res) {
              var html = "";
          
              for (var i = 1; i < res.length; i++) {
                var decodedResponse = decodeEntities(res[i]);
                var tempElement = document.createElement("div");
                tempElement.innerHTML = decodedResponse;
                var decodedContent = tempElement.textContent || tempElement.innerText;
          
                // Build the tip HTML content
                html += `<div class='tip' style='border:3px solid green;border-radius:8px;cursor: pointer;margin-top:10px;text-align:center;' data-tip='${decodedContent}'>${decodedContent}</div>`;
              }
          
              // Inject the HTML content into the tips container
              $('#tips_body').html(html);
          
              // Add a click event listener to each tip
              $('.tip').click(function () {
                var selectedTip = $(this).data('tip');
                appendTipToEditor(selectedTip);
                $('#ques_tips').modal('hide');
              });
          
              // Iterate over each tip and apply the last 14 characters bold logic
              $('.tip').each(function () {
                var tip = $(this);
                var tipContent = tip.data('tip');
                var lastChars = tipContent.substr(-14);
                var hasCapital = /[A-Z]/.test(lastChars);
          
                if (hasCapital) {
                  var modifiedContent = tipContent.replace(lastChars, "<br><strong>" + lastChars + "</strong>");
                  tip.html(modifiedContent);
                }
              });
            },
          });
          
          
        },
      });
    },
    spellchecker_dialog: true,
  };
  vEditor = tinymce.init(MCEConfig);
  function decodeEntities(encodedString) {
    var textArea = document.createElement('textarea');
    textArea.innerHTML = encodedString;
    return textArea.value;
}

// tinymce.init({
//   selector: ".composer-text",
//   forced_root_block: false,
//   force_br_newlines: true,
//   plugins: 'autolink lists link',
//   toolbar: 'undo redo | styleselect | bold italic | bullist numlist | link',
// });
// append tips
function appendTipToEditor(selectedTip) {
  // Get the TinyMCE editor instance
  var editor = tinymce.get(textareaId);

  // Get the current editor content
  var currentContent = editor.getContent();

  // Apply the logic to make the last 14 characters bold
  var lastChars = selectedTip.substr(-14);
  var hasCapital = /[A-Z]/.test(lastChars);

  if (hasCapital) {
    selectedTip = selectedTip.replace(lastChars, "<br><strong>" + lastChars + "</strong>");
  }

  // Append the selected and possibly modified tip to the editor content
  var newContent = currentContent + `<div class="appended_div" style="border:8px solid #76dd6a;border-radius:8px;padding: 8px;margin-top:10px;text-align:center;"><b>Tips</b> ${selectedTip} </div>`;

  // Set the new content in the editor
  editor.setContent(newContent);
}


// 

  //Draw graph 1
  $(".radar-graph").on("click", "#draw_graph_1", function (event) {
    event.preventDefault();
    let line_name = $("#translation").data("user_js_phrase39");
    let graph_header = $("#translation").data("user_js_phrase40");
    draw_radar_graph(this, line_name, graph_header);
  });
  //Draw graph 1 - Respomder
  $(".radar-graph").on("click", "#draw_graph_res_1", function (event) {
    event.preventDefault();
    let line_name = $("#translation").data("user_js_phrase39");
    let graph_header = $("#translation").data("user_js_phrase40");
    let graphTypeCase = $("#choose_graph_res1").val() ?? "1";
    // alert(graphTypeCase);
    if (graphTypeCase == "1") {
      // Radar Graph
      draw_radar_graph_res(this, graph_header);
    } else {
      // Doughnut Graph
      draw_doughnut_graph_res(this, graph_header);
    }
  });

  //------------------------ Add Graph To Editor
  $(".modal-body").on("click", "#add_to_graph_editor", function (e) {
    e.preventDefault();
   
    let image = Chart.getChart("graph_res").toBase64Image();
    tinymce.activeEditor.execCommand(
      "mceInsertContent",
      false,
      `<img src="${image}" alt="Chart Image">`
    );
    $("#graphModal").modal("hide");
  });
  // add table to editor
  $(".modal-body").on("click", "#add_table_to_editor", function (e) {
    e.preventDefault();
    var selectedHtml = `<table border="1" id="gtable" class="table-bordered" style="width: 100%; border-collapse: collapse;"><thead><tr style="border-bottom: 1px solid black;"><th width="50px">Question</th> <th width="390px">Description</th><th width="130px">Category</th><th width="75px">User/Responder</th><th width="55px">Score</th></tr></thead>`;
var selectedBodyHtml = ``;

$('.check_box_row:checked').each(function () {
    var checkboxId = $(this).attr('id');
    selectedBodyHtml+=`<tr>`;
    // Select an element with the ID "row_id + checkboxId"
    var elementId = checkboxId;
    var selectedElement = $('#table_row_' + elementId);
    
    // Get the HTML content of the selected element
    var elementHtml = selectedElement.html();
    
    // Append the HTML content to selectedBodyHtml
    selectedBodyHtml += elementHtml;
    selectedBodyHtml+=`</tr>`;
});

selectedHtml += selectedBodyHtml;
selectedHtml += `</tbody></table>`;

// Insert the selected HTML content into the TinyMCE editor
tinymce.activeEditor.execCommand("mceInsertContent", false, selectedHtml);

$("#tableModal").modal("hide");

  });
  // add categories table to editor
  $(".modal-body").on("click", "#add_categories_table_to_editor", function (e) {
    e.preventDefault();
    var selectedHtml = `<table border="1" id="ctable" class="table-bordered" style="width: 100%; border-collapse: collapse;"><thead><tr style="border-bottom: 1px solid black;"><th width="130px">Category No</th><th width="75px">Category Name</th><th width="75px">User/Responder</th><th width="55px">Score</th></tr></thead>`;
var selectedBodyHtml = ``;

$('.check_box_row_categories:checked').each(function () {
    var checkboxId = $(this).attr('id');
    selectedBodyHtml+=`<tr>`;
    // Select an element with the ID "row_id + checkboxId"
    var elementId = checkboxId;
    var selectedElement = $('#cattable_row_' + elementId);
    
    // Get the HTML content of the selected element
    var elementHtml = selectedElement.html();
    
    // Append the HTML content to selectedBodyHtml
    selectedBodyHtml += elementHtml;
    selectedBodyHtml+=`</tr>`;
});

selectedHtml += selectedBodyHtml;
selectedHtml += `</tbody></table>`;

// Insert the selected HTML content into the TinyMCE editor
tinymce.activeEditor.execCommand("mceInsertContent", false, selectedHtml);

$("#categoriesModal").modal("hide");

  });
  // end
  // add company  categories table to editor
  $(".modal-body").on("click", "#add_companies_categories_table_to_editor", function (e) {
    e.preventDefault();
    var selectedHtml = `<table border="1" id="cctable" class="table-bordered" style="width: 100%; border-collapse: collapse;"><thead><tr style="border-bottom: 1px solid black;"><th width="10px">Serial No</th><th width="370px">Categories</th><th width="30px">User/Responder</th><th width="55px">Score Value</th></tr></thead>`;
var selectedBodyHtml = ``;

$('.check_box_row_company_categories:checked').each(function () {
    var checkboxId = $(this).attr('id');
    selectedBodyHtml+=`<tr>`;
    // Select an element with the ID "row_id + checkboxId"
    var elementId = checkboxId;
    var selectedElement = $('#ccattable_row_' + elementId);
    
    // Get the HTML content of the selected element
    var elementHtml = selectedElement.html();
    
    // Append the HTML content to selectedBodyHtml
    selectedBodyHtml += elementHtml;
    selectedBodyHtml+=`</tr>`;
});

selectedHtml += selectedBodyHtml;
selectedHtml += `</tbody></table>`;

// Insert the selected HTML content into the TinyMCE editor
tinymce.activeEditor.execCommand("mceInsertContent", false, selectedHtml);

$("#categoriesCompanyModal").modal("hide");

  });
  // end
 

  $("#graphModal").on("show.bs.modal", function () {
    $("#draw_graph_res_1").trigger("click");
  });

  $("#graphModal").on("hide.bs.modal", function () {});

  //Save report
  $(".save_mlreport_common_format").on("click", function (event) {
    event.preventDefault();

    $(this).prop("disabled", true);
    $(this).append(' <i class="fas fa-spinner  fa-fw fa-spin"></i>');

    var req_type = $(this).data("req_type");

    let report = {};
    let submission = true;
    $("#free_text_15").addClass("show");

    //Front page
    let front_page_enable = $(
      "#composer_frontpage_heading .section-checker"
    ).is(":checked");
    let username = $("#composer_username").is(":checked");
    let company_name = $("#composer_company_name").is(":checked");
    let logo = $("#composer_company_logo").is(":checked");
    let ticket_id = $("#composer_ticket_id").is(":checked");
    let page_break1 = $("#composer_page_break1").is(":checked");
    report["front_page"] = {
      enabled: front_page_enable,
      username: username,
      company_name: company_name,
      logo: logo,
      ticket_id: ticket_id,
      page_break1: page_break1,
    };
    //Free Text 1
    let free_text1_enable = $(
      "#composer_freetext1_heading .section-checker"
    ).is(":checked");
    let free_text1 = tinymce.editors["composer_text_1"].getContent();
    //console.log(free_text1);
    //return false;
    let page_break2 = $("#composer_page_break2").is(":checked");
    report["free_text1"] = {
      enabled: free_text1_enable,
      text: free_text1,
      page_break2: page_break2,
    };
    //Intro Text
    let intro_text_enable = $(
      "#composer_introtext_heading .section-checker"
    ).is(":checked");
    let intro_text = tinymce.editors["composer_text_2"].getContent();
    let page_break3 = $("#composer_page_break3").is(":checked");
    report["intro_text"] = {
      enabled: intro_text_enable,
      text: intro_text,
      page_break3: page_break3,
    };

    //Free Text 2
    let free_text2_enable = $(
      "#composer_freetext2_heading .section-checker"
    ).is(":checked");
    let free_text2 = tinymce.editors["composer_text_3"].getContent();
    let page_break4 = $("#composer_page_break4").is(":checked");
    report["free_text2"] = {
      enabled: free_text2_enable,
      text: free_text2,
      page_break4: page_break4,
    };

    //Free Text 3
    let free_text3_enable = $(
      "#composer_freetext3_heading .section-checker"
    ).is(":checked");
    let free_text3 = tinymce.editors["composer_text_4"].getContent();
    let page_break5 = $("#composer_page_break5").is(":checked");
    report["free_text3"] = {
      enabled: free_text3_enable,
      text: free_text3,
      page_break5: page_break5,
    };

    //Free Text 4
    if ($("#composer_freetext4_heading .section-checker").is(":checked")) {
      var img = tinymce.editors["composer_text_5"].dom.get("catGraphPlacement");
      // tinymce.editors["composer_text_5"].dom.remove(img);

      var catImg = $("#catChart").val();

      // tinymce.editors["composer_text_5"].insertContent(
      //   '<div id="catGraphPlacement" style="width:100%;text-align: center"><img src="' +
      //     catImg +
      //     '" style="width:500px;height:500px;margin-left:auto;margin-rigth:auto;" /></div>'
      // );

      let free_text4_enable = $(
        "#composer_freetext4_heading .section-checker"
      ).is(":checked");
      let free_text4 = tinymce.editors["composer_text_5"].getContent();
      let page_break6 = $("#composer_page_break6").is(":checked");
      report["free_text4"] = {
        enabled: free_text4_enable,
        text: free_text4,
        page_break6: page_break6,
      };
    }

    //Free Text 5
    if ($("#composer_freetext5_heading .section-checker").is(":checked")) {
      var img = tinymce.editors["composer_text_6"].dom.get(
        "radarGraphPlacement"
      );
      // tinymce.editors["composer_text_6"].dom.remove(img);

      /*var canvasImg = document.getElementById("radar_graph_1");
      var dataURL = canvasImg.toDataURL("image/png");

      // tinymce.editors['composer_text_6'].insertContent('<img id="radarGraphPlacement" src="' + dataURL + '"/>');

      //TODO: Maybe shall need to remove this Kawsar

      var editorContent = tinymce.get("composer_text_6").getContent();
      var imageArea = editorContent.match(
        /<div id="radarGraphImgArea">(.*?)<\/div>/i
      );
      if (imageArea) {
        tinymce
          .get("composer_text_6")
          .setContent(
            editorContent.replace(
              imageArea[0],
              imageArea[0].replace(
                imageArea[1],
                imageArea[1] +
                  '<div id="radarGraphPlacement" style="width:100%;text-align: center"><img src="' +
                  dataURL +
                  '" style="width:600px;height:430px;"/></div>'
              )
            )
          );
      }*/

      let free_text5_enable = $(
        "#composer_freetext5_heading .section-checker"
      ).is(":checked");
      let free_text5 = tinymce.editors["composer_text_6"].getContent();
      let page_break7 = $("#composer_page_break7").is(":checked");
      report["free_text5"] = {
        enabled: free_text5_enable,
        text: free_text5,
        page_break7: page_break7,
      };
    }

    //Free Text 6
    let free_text6_enable = $(
      "#composer_freetext6_heading .section-checker"
    ).is(":checked");
    let free_text6 = tinymce.editors["composer_text_7"].getContent();
    let page_break8 = $("#composer_page_break8").is(":checked");
    report["free_text6"] = {
      enabled: free_text6_enable,
      text: free_text6,
      page_break8: page_break8,
    };

    //Free Text 7
    let free_text7_enable = $(
      "#composer_freetext7_heading .section-checker"
    ).is(":checked");
    let free_text7 = tinymce.editors["composer_text_8"].getContent();
    let page_break9 = $("#composer_page_break9").is(":checked");
    report["free_text7"] = {
      enabled: free_text7_enable,
      text: free_text7,
      page_break9: page_break9,
    };

    //Methods

    report["methods"] = [];
    $(".method_data_name").each(function () {
      // Check if the checkbox is checked
      if ($(this).is(":checked")) {
        // Extract the value of the "data-custom" attribute
        var dataCustomValue = $(this).attr("data-custom");

        // Use the extracted value to select the corresponding editor element
        var editorValue = tinymce.editors[dataCustomValue].getContent();

        // Do whatever you need to do with the editor value here

        if (editorValue != "") {
          report["methods"].push({
            enabled: true,
            text: editorValue,
            method_name: $(this).val(),
          });
        }
      }
    });

    //Free Text 8
    let free_text8_enable = $(
      "#composer_freetext8_heading .section-checker"
    ).is(":checked");
    let free_text8 = tinymce.editors["composer_text_9"].getContent();
    let page_break10 = $("#composer_page_break10").is(":checked");
    report["free_text8"] = {
      enabled: free_text8_enable,
      text: free_text8,
      page_break10: page_break10,
    };

    //Free Text 9
    let free_text9_enable = $(
      "#composer_freetext9_heading .section-checker"
    ).is(":checked");
    let free_text9 = tinymce.editors["composer_text_10"].getContent();
    let page_break11 = $("#composer_page_break11").is(":checked");
    report["free_text9"] = {
      enabled: free_text9_enable,
      text: free_text9,
      page_break11: page_break11,
    };

    //Free Text 10
    let free_text10_enable = $(
      "#composer_freetext10_heading .section-checker"
    ).is(":checked");
    let free_text10 = tinymce.editors["composer_text_11"].getContent();
    let page_break12 = $("#composer_page_break12").is(":checked");
    report["free_text10"] = {
      enabled: free_text10_enable,
      text: free_text10,
      page_break12: page_break12,
    };

    //Free Text 11
    let free_text11_enable = $(
      "#composer_freetext11_heading .section-checker"
    ).is(":checked");
    let free_text11 = tinymce.editors["composer_text_12"].getContent();
    let page_break13 = $("#composer_page_break13").is(":checked");
    report["free_text11"] = {
      enabled: free_text11_enable,
      text: free_text11,
      page_break13: page_break13,
    };

    //Free Text 12
    let free_text12_enable = $(
      "#composer_freetext12_heading .section-checker"
    ).is(":checked");
    let free_text12 = tinymce.editors["composer_text_13"].getContent();
    let page_break14 = $("#composer_page_break14").is(":checked");
    report["free_text12"] = {
      enabled: free_text12_enable,
      text: free_text12,
      page_break14: page_break14,
    };

    //Free Text 13
    let free_text13_enable = $(
      "#composer_freetext13_heading .section-checker"
    ).is(":checked");
    let free_text13 = tinymce.editors["composer_text_14"].getContent();
    let page_break15 = $("#composer_page_break15").is(":checked");
    report["free_text13"] = {
      enabled: free_text13_enable,
      text: free_text13,
      page_break15: page_break15,
    };

    //Free Text 14
    let free_text14_enable = $(
      "#composer_freetext14_heading .section-checker"
    ).is(":checked");
    let free_text14 = tinymce.editors["composer_text_15"].getContent();
    let page_break16 = $("#composer_page_break16").is(":checked");
    report["free_text14"] = {
      enabled: free_text14_enable,
      text: free_text14,
      page_break16: page_break16,
    };
    if ($("#composer_freetext15_heading .section-checker").is(":checked")) {
      //Free Text 15 - Radar graph

      let free_text15_enable = $(
        "#composer_freetext15_heading .section-checker"
      ).is(":checked");
      let free_text15 = tinymce.editors["composer_text_16"].getContent();
      let page_break17 = $("#composer_page_break17").is(":checked");

      report["free_text15"] = {
        enabled: free_text15_enable,
        text: free_text15,
        page_break17: page_break17,
      };
    } //else{
    //     alert($('#translation').data('user_js_phrase45'));
    //     submission = false;
    // }

    json_report = JSON.stringify(report);

    let ticket_id_post = $("#ticket_id").val();
    let lang_code = $("#lang_code").val();

    if (submission) {
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "create_mlreport_common_format",
          ticket_id: ticket_id_post,
          content: json_report,
          lang_code: lang_code,
        },
      }).done(function (data) {
        $(".save_mlreport_common_format").prop("disabled", false);
        $(".save_mlreport_common_format i").remove();

        if (data == "success") {
          // location.reload();
          alert($("#translation").data("user_js_phrase44"));

          if (req_type === "publish") {
            window.location.href =
              "?route=ticket&id=" +
              ticket_id_post +
              "&page=question&_type=report_genrate";
          }
        } else alert(data);
      });
    }
  });

  $(".radar-graph").on("click", "#draw_graph_2", function (event) {
    event.preventDefault();
    let line_name = $("#translation").data("user_js_phrase41");
    let graph_header = $("#translation").data("user_js_phrase42");
    draw_radar_graph(this, line_name, graph_header);
  });
  //Save report
  $(".report-composer").on("click", "#save_report", function (event) {
    event.preventDefault();

    let report = {};
    let submission = true;

    //Front page
    let front_page_enable = $(
      "#composer_frontpage_heading .section-checker"
    ).is(":checked");
    let username = $("#composer_username").is(":checked");
    let company_name = $("#composer_company_name").is(":checked");
    let logo = $("#composer_company_logo").is(":checked");
    let ticket_id = $("#composer_ticket_id").is(":checked");
    let page_break1 = $("#composer_page_break1").is(":checked");
    report["front_page"] = {
      enabled: front_page_enable,
      username: username,
      company_name: company_name,
      logo: logo,
      ticket_id: ticket_id,
      page_break1: page_break1,
    };
    //Free Text 1
    let free_text1_enable = $(
      "#composer_freetext1_heading .section-checker"
    ).is(":checked");
    let free_text1 = tinymce.editors["composer_text_1"].getContent();
    let page_break2 = $("#composer_page_break2").is(":checked");
    report["free_text1"] = {
      enabled: free_text1_enable,
      text: free_text1,
      page_break2: page_break2,
    };
    //Intro Text
    let intro_text_enable = $(
      "#composer_introtext_heading .section-checker"
    ).is(":checked");
    let intro_text = tinymce.editors["composer_text_2"].getContent();
    let page_break3 = $("#composer_page_break3").is(":checked");
    report["intro_text"] = {
      enabled: intro_text_enable,
      text: intro_text,
      page_break3: page_break3,
    };
    //Free Text 2
    let free_text2_enable = $(
      "#composer_freetext2_heading .section-checker"
    ).is(":checked");
    let free_text3 = tinymce.editors["composer_text_3"].getContent();
    let page_break4 = $("#composer_page_break4").is(":checked");
    report["free_text2"] = {
      enabled: free_text2_enable,
      text: free_text3,
      page_break4: page_break4,
    };
    //Conflict Text
    let conflict_enable = $("#composer_conflict_heading .section-checker").is(
      ":checked"
    );
    let conflict_text = tinymce.editors["composer_text_4"].getContent();
    let page_break5 = $("#composer_page_break5").is(":checked");
    report["conflict"] = {
      enabled: conflict_enable,
      text: conflict_text,
      page_break5: page_break5,
    };
    //Free Text 3
    let free_text3_enable = $(
      "#composer_freetext3_heading .section-checker"
    ).is(":checked");
    let free_text5 = tinymce.editors["composer_text_5"].getContent();
    let page_break6 = $("#composer_page_break6").is(":checked");
    report["free_text3"] = {
      enabled: free_text3_enable,
      text: free_text5,
      page_break6: page_break6,
    };
    //Summary
    let summary_enable = $("#composer_summary_heading .section-checker").is(
      ":checked"
    );
    let summary_text = $("#composer_summary_text").html();
    let page_break7 = $("#composer_page_break7").is(":checked");
    report["summary"] = {
      enabled: summary_enable,
      text: summary_text,
      page_break7: page_break7,
    };
    //Free Text 4
    let free_text4_enable = $(
      "#composer_freetext4_heading .section-checker"
    ).is(":checked");
    let free_text6a = tinymce.editors["composer_text_6a"].getContent();
    let free_text6b = tinymce.editors["composer_text_6b"].getContent();
    let page_break8 = $("#composer_page_break8").is(":checked");
    report["free_text4"] = {
      enabled: free_text4_enable,
      text: free_text6a,
      text2: free_text6b,
      page_break8: page_break8,
    };
    //Radar Graph
    let graph_enable = $("#composer_graph_heading .section-checker").is(
      ":checked"
    );
    let graph_header = $("#composer_radar_graph_header").val();
    let graph1_ctn = $("#radar_graph_1").find("canvas");
    //let graph2_ctn = $('#radar_graph_2').find('canvas');
    let graph1_canvas = document.getElementById(graph1_ctn.attr("id"));
    //let graph2_canvas = document.getElementById(graph2_ctn.attr('id'));
    let graph1 = "";
    let graph2 = "";
    if (graph_enable) {
      //if (graph1_ctn.length && graph2_ctn.length) {
      if (graph1_ctn.length) {
        graph1 = graph1_canvas.toDataURL("image/jpeg");
        //graph2 = graph2_canvas.toDataURL("image/jpeg");
      } else {
        alert($("#translation").data("user_js_phrase43"));
        submission = false;
      }
    }
    let free_text8 = tinymce.editors["composer_text_8"].getContent();
    let page_break9 = $("#composer_page_break9").is(":checked");
    report["radar_graph"] = {
      enabled: graph_enable,
      header: graph_header,
      graph1: graph1,
      graph2: graph2,
      text: free_text8,
      page_break9: page_break9,
    };
    //Free Text 6
    let free_text6_enable = $(
      "#composer_freetext6_heading .section-checker"
    ).is(":checked");
    let free_text9 = tinymce.editors["composer_text_9"].getContent();
    let page_break10 = $("#composer_page_break10").is(":checked");
    report["free_text6"] = {
      enabled: free_text6_enable,
      text: free_text9,
      page_break10: page_break10,
    };

    //Review
    let review_enable = $("#chk_review_check").is(":checked");
    let page_break_review = $("#composer_page_break_review").is(":checked");
    report["review"] = {
      enabled: review_enable,
      page_break_review: page_break_review,
    };

    //Rating
    let rating_enable = $("#chk_rating_check").is(":checked");
    let page_break_rating = $("#composer_page_break_rating").is(":checked");
    report["rating"] = {
      enabled: rating_enable,
      page_break_rating: page_break_rating,
    };

    //Methods
    let method_enable = $("#composer_methods_heading .section-checker").is(
      ":checked"
    );
    let header_text = $(".composer-method-header-text").html();
    let methods = $("#ranked_methods").val();
    let page_break11 = $("#composer_page_break11").is(":checked");
    report["method"] = {
      enabled: method_enable,
      header_text: header_text,
      methods: methods,
      page_break11: page_break11,
    };
    //Free Text 7
    let free_text7_enable = $(
      "#composer_freetext7_heading .section-checker"
    ).is(":checked");
    let free_text10 = tinymce.editors["composer_text_10"].getContent();
    let page_break12 = $("#composer_page_break12").is(":checked");
    report["free_text7"] = {
      enabled: free_text7_enable,
      text: free_text10,
      page_break12: page_break12,
    };
    //Tips
    let tips_enable = $("#composer_tips_heading .section-checker").is(
      ":checked"
    );
    let tips = $("#question_tips").val();
    let page_break13 = $("#composer_page_break13").is(":checked");
    report["tip"] = {
      enabled: tips_enable,
      tips: tips,
      page_break13: page_break13,
    };
    //Free Text 8
    let free_text8_enable = $(
      "#composer_freetext8_heading .section-checker"
    ).is(":checked");
    let free_text11 = tinymce.editors["composer_text_11"].getContent();
    let page_break14 = $("#composer_page_break14").is(":checked");
    report["free_text8"] = {
      enabled: free_text8_enable,
      text: free_text11,
      page_break14: page_break14,
    };
    //Disclaimer
    let disclaimer_enable = $(
      "#composer_disclaimer_heading .section-checker"
    ).is(":checked");
    let disclaimer_text = tinymce.editors["composer_text_12"].getContent();
    let free_text13 = tinymce.editors["composer_text_13"].getContent();
    let page_break15 = $("#composer_page_break15").is(":checked");
    report["disclaimer"] = {
      enabled: disclaimer_enable,
      disclaimer_text: disclaimer_text,
      free_text: free_text13,
      page_break15: page_break15,
    };
    //Assesment
    let assessment_enable = $(
      "#composer_assessment_heading .section-checker"
    ).is(":checked");

    let risks = {};
    let risksRumours = [];
    $(".risk_type_rumours").each(function () {
      risksRumours.push($(this).html());
    });
    risks["rumours"] = risksRumours;

    let risksNotEnoughInfo = [];
    $(".risk_type_not_enough_info").each(function () {
      risksNotEnoughInfo.push($(this).html());
    });
    risks["notEnoughInfo"] = risksNotEnoughInfo;

    let risksInaccessible = [];
    $(".risk_type_inaccessible_leader").each(function () {
      risksInaccessible.push($(this).html());
    });
    risks["inaccessibleLeader"] = risksInaccessible;

    let risksSickleave = [];
    $(".risk_type_sickleave").each(function () {
      risksSickleave.push($(this).html());
    });
    risks["sickleave"] = risksSickleave;

    let risksPolarization = [];
    $(".risk_type_polarization").each(function () {
      risksPolarization.push($(this).html());
    });
    risks["polarization"] = risksPolarization;

    report["assessment"] = {
      enabled: assessment_enable,
      risks: risks,
    };

    json_report = JSON.stringify(report);

    let ticket = $("#ticket_id").val();
    let lang_code = $("#lang_code").val();

    if (submission) {
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "create_report",
          ticket_id: ticket,
          content: json_report,
          lang_code: lang_code,
        },
      }).done(function (data) {
        if (data == "success") {
          alert($("#translation").data("user_js_phrase44"));
          window.location.href =
            "?route=ticket&id=" + ticket + "&page=question";
        } else alert(data);
      });
    }
  });

  //Save report
  $("#save_graph_report").on("click", function (event) {
    let report = {};
    let submission = true;

    //Radar Graph
    let graph1_ctn = $("#radar_graph_1").find("canvas");
    let graph1_canvas = document.getElementById(graph1_ctn.attr("id"));
    let graph1 = "";

    graph1 = graph1_canvas.toDataURL("image/jpeg");
    let ticket = $("#ticket_id_data").val();

    report["radar_graph"] = {
      graph1: graph1,
    };

    json_report = JSON.stringify(report);

    let lang_code = $("#lang_code").val();

    if (submission) {
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "create_graph_report",
          ticket_id: ticket,
          content: json_report,
          lang_code: lang_code,
        },
      }).done(function (data) {
        if (data == "success") {
          $("#success_message").html("<b>Graph Saved!</b>");
          $("#success_message").delay(2000).fadeOut();
        } else $("#success_message").html("<b>Graph Not Saved!</b>");
        $("#success_message").delay(2000).fadeOut();
      });
    }
  });

  $("#save_graph_res_report").on("click", function (event) {
    let report = {};
    let submission = true;

    //Radar Graph
    let graph1_ctn = $("#radar_graph_1").find("canvas");
    let graph1_canvas = document.getElementById(graph1_ctn.attr("id"));
    let graph1 = "";

    graph1 = graph1_canvas.toDataURL("image/png");

    let ticket_id_post = $("#ticket_id").val();
    report["graph"] = {
      radar_graph: graph1,
      cat_graph: document.getElementById("catChart").value,
    };

    json_report = JSON.stringify(report);

    let lang_code = $("#lang_code").val();

    if (submission) {
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "create_graph_report",
          ticket_id: ticket_id_post,
          content: json_report,
          lang_code: lang_code,
        },
      }).done(function (data) {
        if (data == "success") {
          console.log("saved");
        } else {
          console.log("failed");
        }
      });
    }
  });

  //mlSave report format composed
  $(".report-dcomposer").on("click", "#save_mlreport_format", function (event) {
    event.preventDefault();

    let report = {};
    let submission = true;

    //Front page
    let front_page_enable = $(
      "#composer_frontpage_heading .section-checker"
    ).is(":checked");
    let username = $("#composer_username").is(":checked");
    let company_name = $("#composer_company_name").is(":checked");
    let logo = $("#composer_company_logo").is(":checked");
    let ticket_id = $("#composer_ticket_id").is(":checked");
    let page_break1 = $("#composer_page_break1").is(":checked");
    report["front_page"] = {
      enabled: front_page_enable,
      username: username,
      company_name: company_name,
      logo: logo,
      ticket_id: ticket_id,
      page_break1: page_break1,
    };
    //Free Text 1
    let free_text1_enable = $(
      "#composer_freetext1_heading .section-checker"
    ).is(":checked");
    let free_text1 = tinymce.editors["composer_text_1"].getContent();
    //console.log(free_text1);
    //return false;
    let page_break2 = $("#composer_page_break2").is(":checked");
    report["free_text1"] = {
      enabled: free_text1_enable,
      text: free_text1,
      page_break2: page_break2,
    };
    //Intro Text
    let intro_text_enable = $(
      "#composer_introtext_heading .section-checker"
    ).is(":checked");
    let intro_text = tinymce.editors["composer_text_2"].getContent();
    let page_break3 = $("#composer_page_break3").is(":checked");
    report["intro_text"] = {
      enabled: intro_text_enable,
      text: intro_text,
      page_break3: page_break3,
    };

    //Free Text 2
    let free_text2_enable = $(
      "#composer_freetext2_heading .section-checker"
    ).is(":checked");
    let free_text2 = tinymce.editors["composer_text_3"].getContent();
    let page_break4 = $("#composer_page_break4").is(":checked");
    report["free_text2"] = {
      enabled: free_text2_enable,
      text: free_text2,
      page_break4: page_break4,
    };

    //Free Text 3
    let free_text3_enable = $(
      "#composer_freetext3_heading .section-checker"
    ).is(":checked");
    let free_text3 = tinymce.editors["composer_text_4"].getContent();
    let page_break5 = $("#composer_page_break5").is(":checked");
    report["free_text3"] = {
      enabled: free_text3_enable,
      text: free_text3,
      page_break5: page_break5,
    };

    //Free Text 4
    let free_text4_enable = $(
      "#composer_freetext4_heading .section-checker"
    ).is(":checked");
    let free_text4 = tinymce.editors["composer_text_5"].getContent();
    let page_break6 = $("#composer_page_break6").is(":checked");
    report["free_text4"] = {
      enabled: free_text4_enable,
      text: free_text4,
      page_break6: page_break6,
    };

    //Free Text 5
    let free_text5_enable = $(
      "#composer_freetext5_heading .section-checker"
    ).is(":checked");
    let free_text5 = tinymce.editors["composer_text_6"].getContent();
    let page_break7 = $("#composer_page_break7").is(":checked");
    report["free_text5"] = {
      enabled: free_text5_enable,
      text: free_text5,
      page_break7: page_break7,
    };

    //Free Text 6
    let free_text6_enable = $(
      "#composer_freetext6_heading .section-checker"
    ).is(":checked");
    let free_text6 = tinymce.editors["composer_text_7"].getContent();
    let page_break8 = $("#composer_page_break8").is(":checked");
    report["free_text6"] = {
      enabled: free_text6_enable,
      text: free_text6,
      page_break8: page_break8,
    };

    //Free Text 7
    let free_text7_enable = $(
      "#composer_freetext7_heading .section-checker"
    ).is(":checked");
    let free_text7 = tinymce.editors["composer_text_8"].getContent();
    let page_break9 = $("#composer_page_break9").is(":checked");
    report["free_text7"] = {
      enabled: free_text7_enable,
      text: free_text7,
      page_break9: page_break9,
    };

    //Free Text 8
    let free_text8_enable = $(
      "#composer_freetext8_heading .section-checker"
    ).is(":checked");
    let free_text8 = tinymce.editors["composer_text_9"].getContent();
    let page_break10 = $("#composer_page_break10").is(":checked");
    report["free_text8"] = {
      enabled: free_text8_enable,
      text: free_text8,
      page_break10: page_break10,
    };

    //Free Text 9
    let free_text9_enable = $(
      "#composer_freetext9_heading .section-checker"
    ).is(":checked");
    let free_text9 = tinymce.editors["composer_text_10"].getContent();
    let page_break11 = $("#composer_page_break11").is(":checked");
    report["free_text9"] = {
      enabled: free_text9_enable,
      text: free_text9,
      page_break11: page_break11,
    };

    //Free Text 10
    let free_text10_enable = $(
      "#composer_freetext10_heading .section-checker"
    ).is(":checked");
    let free_text10 = tinymce.editors["composer_text_11"].getContent();
    let page_break12 = $("#composer_page_break12").is(":checked");
    report["free_text10"] = {
      enabled: free_text10_enable,
      text: free_text10,
      page_break12: page_break12,
    };

    //Free Text 11
    let free_text11_enable = $(
      "#composer_freetext11_heading .section-checker"
    ).is(":checked");
    let free_text11 = tinymce.editors["composer_text_12"].getContent();
    let page_break13 = $("#composer_page_break13").is(":checked");
    report["free_text11"] = {
      enabled: free_text11_enable,
      text: free_text11,
      page_break13: page_break13,
    };

    //Free Text 12
    let free_text12_enable = $(
      "#composer_freetext12_heading .section-checker"
    ).is(":checked");
    let free_text12 = tinymce.editors["composer_text_13"].getContent();
    let page_break14 = $("#composer_page_break14").is(":checked");
    report["free_text12"] = {
      enabled: free_text12_enable,
      text: free_text12,
      page_break14: page_break14,
    };

    //Free Text 13
    let free_text13_enable = $(
      "#composer_freetext13_heading .section-checker"
    ).is(":checked");
    let free_text13 = tinymce.editors["composer_text_14"].getContent();
    let page_break15 = $("#composer_page_break15").is(":checked");
    report["free_text13"] = {
      enabled: free_text13_enable,
      text: free_text13,
      page_break15: page_break15,
    };

    //Free Text 14
    let free_text14_enable = $(
      "#composer_freetext14_heading .section-checker"
    ).is(":checked");
    let free_text14 = tinymce.editors["composer_text_15"].getContent();
    let page_break16 = $("#composer_page_break16").is(":checked");
    report["free_text14"] = {
      enabled: free_text14_enable,
      text: free_text14,
      page_break16: page_break16,
    };
    if ($("#composer_freetext15_heading .section-checker").is(":checked")) {
      //Free Text 15 - Radar graph
      let free_text15_enable = $(
        "#composer_freetext15_heading .section-checker"
      ).is(":checked");
      let free_text15 = tinymce.editors["composer_text_16"].getContent();
      let page_break17 = $("#composer_page_break17").is(":checked");

      report["free_text15"] = {
        enabled: free_text15_enable,
        text: free_text15,
        page_break17: page_break17,
      };
    } else {
      alert($("#translation").data("user_js_phrase45"));
      submission = false;
    }

    json_report = JSON.stringify(report);

    let report_format = $("#report_format_id").val();
    let lang_code = $("#report_lang_code").val();

    if (submission) {
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "create_mlreport_format",
          report_format_id: report_format,
          content: json_report,
          lang_code: lang_code,
        },
      }).done(function (data) {
        if (data == "success") {
          alert($("#translation").data("user_js_phrase44"));
          //window.location.href = '?route=report_format_dcomposer&id=' + ticket + '&page=question';
          location.reload();
        } else alert(data);
      });
    }
  });

  //Save report format composed
  $(".report-dcomposer").on(
    "click",
    "#save_report_format_dcomposed",
    function (event) {
      event.preventDefault();

      let report = {};
      let submission = true;

      //Front page
      let front_page_enable = $(
        "#composer_frontpage_heading .section-checker"
      ).is(":checked");
      let username = $("#composer_username").is(":checked");
      let company_name = $("#composer_company_name").is(":checked");
      let logo = $("#composer_company_logo").is(":checked");
      let ticket_id = $("#composer_ticket_id").is(":checked");
      let page_break1 = $("#composer_page_break1").is(":checked");
      report["front_page"] = {
        enabled: front_page_enable,
        username: username,
        company_name: company_name,
        logo: logo,
        ticket_id: ticket_id,
        page_break1: page_break1,
      };
      //Free Text 1
      let free_text1_enable = $(
        "#composer_freetext1_heading .section-checker"
      ).is(":checked");
      let free_text1 = tinymce.editors["composer_text_1"].getContent();
      let page_break2 = $("#composer_page_break2").is(":checked");
      //console.log(free_text1);
      // return false;
      report["free_text1"] = {
        enabled: free_text1_enable,
        text: free_text1,
        page_break2: page_break2,
      };
      //Intro Text
      let intro_text_enable = $(
        "#composer_introtext_heading .section-checker"
      ).is(":checked");
      let intro_text = tinymce.editors["composer_text_2"].getContent();
      let page_break3 = $("#composer_page_break3").is(":checked");
      report["intro_text"] = {
        enabled: intro_text_enable,
        text: intro_text,
        page_break3: page_break3,
      };

      //Free Text 2
      let free_text2_enable = $(
        "#composer_freetext2_heading .section-checker"
      ).is(":checked");
      let free_text2 = tinymce.editors["composer_text_3"].getContent();
      let page_break4 = $("#composer_page_break4").is(":checked");
      report["free_text2"] = {
        enabled: free_text2_enable,
        text: free_text2,
        page_break4: page_break4,
      };

      //Free Text 3
      let free_text3_enable = $(
        "#composer_freetext3_heading .section-checker"
      ).is(":checked");
      let free_text3 = tinymce.editors["composer_text_4"].getContent();
      let page_break5 = $("#composer_page_break5").is(":checked");
      report["free_text3"] = {
        enabled: free_text3_enable,
        text: free_text3,
        page_break5: page_break5,
      };

      //Free Text 4
      let free_text4_enable = $(
        "#composer_freetext4_heading .section-checker"
      ).is(":checked");
      let free_text4 = tinymce.editors["composer_text_5"].getContent();
      let page_break6 = $("#composer_page_break6").is(":checked");
      report["free_text4"] = {
        enabled: free_text4_enable,
        text: free_text4,
        page_break6: page_break6,
      };

      //Free Text 5
      let free_text5_enable = $(
        "#composer_freetext5_heading .section-checker"
      ).is(":checked");
      let free_text5 = tinymce.editors["composer_text_6"].getContent();
      let page_break7 = $("#composer_page_break7").is(":checked");
      report["free_text5"] = {
        enabled: free_text5_enable,
        text: free_text5,
        page_break7: page_break7,
      };

      //Free Text 6
      let free_text6_enable = $(
        "#composer_freetext6_heading .section-checker"
      ).is(":checked");
      let free_text6 = tinymce.editors["composer_text_7"].getContent();
      let page_break8 = $("#composer_page_break8").is(":checked");
      report["free_text6"] = {
        enabled: free_text6_enable,
        text: free_text6,
        page_break8: page_break8,
      };

      //Free Text 7
      let free_text7_enable = $(
        "#composer_freetext7_heading .section-checker"
      ).is(":checked");
      let free_text7 = tinymce.editors["composer_text_8"].getContent();
      let page_break9 = $("#composer_page_break9").is(":checked");
      report["free_text7"] = {
        enabled: free_text7_enable,
        text: free_text7,
        page_break9: page_break9,
      };

      //Free Text 8
      let free_text8_enable = $(
        "#composer_freetext8_heading .section-checker"
      ).is(":checked");
      let free_text8 = tinymce.editors["composer_text_9"].getContent();
      let page_break10 = $("#composer_page_break10").is(":checked");
      report["free_text8"] = {
        enabled: free_text8_enable,
        text: free_text8,
        page_break10: page_break10,
      };

      //Free Text 9
      let free_text9_enable = $(
        "#composer_freetext9_heading .section-checker"
      ).is(":checked");
      let free_text9 = tinymce.editors["composer_text_10"].getContent();
      let page_break11 = $("#composer_page_break11").is(":checked");
      report["free_text9"] = {
        enabled: free_text9_enable,
        text: free_text9,
        page_break11: page_break11,
      };

      //Free Text 10
      let free_text10_enable = $(
        "#composer_freetext10_heading .section-checker"
      ).is(":checked");
      let free_text10 = tinymce.editors["composer_text_11"].getContent();
      let page_break12 = $("#composer_page_break12").is(":checked");
      report["free_text10"] = {
        enabled: free_text10_enable,
        text: free_text10,
        page_break12: page_break12,
      };

      //Free Text 11
      let free_text11_enable = $(
        "#composer_freetext11_heading .section-checker"
      ).is(":checked");
      let free_text11 = tinymce.editors["composer_text_12"].getContent();
      let page_break13 = $("#composer_page_break13").is(":checked");
      report["free_text11"] = {
        enabled: free_text11_enable,
        text: free_text11,
        page_break13: page_break13,
      };

      //Free Text 12
      let free_text12_enable = $(
        "#composer_freetext12_heading .section-checker"
      ).is(":checked");
      let free_text12 = tinymce.editors["composer_text_13"].getContent();
      let page_break14 = $("#composer_page_break14").is(":checked");

      let graph1_ctn = $("#radar_graph_1").find("canvas");
      let graph1_canvas = document.getElementById(graph1_ctn.attr("id"));
      let graph1 = "";

      graph1 = graph1_canvas.toDataURL("image/jpeg");

      report["free_text12"] = {
        enabled: free_text12_enable,
        text: free_text12,
        page_break14: page_break14,
      };

      //Free Text 13
      let free_text13_enable = $(
        "#composer_freetext13_heading .section-checker"
      ).is(":checked");
      let free_text13 = tinymce.editors["composer_text_14"].getContent();
      let page_break15 = $("#composer_page_break15").is(":checked");
      report["free_text13"] = {
        enabled: free_text13_enable,
        text: free_text13,
        page_break15: page_break15,
      };

      //Free Text 14
      let free_text14_enable = $(
        "#composer_freetext14_heading .section-checker"
      ).is(":checked");
      let free_text14 = tinymce.editors["composer_text_15"].getContent();
      let page_break16 = $("#composer_page_break16").is(":checked");
      report["free_text14"] = {
        enabled: free_text14_enable,
        text: free_text14,
        page_break16: page_break16,
      };

      json_report = JSON.stringify(report);

      let report_format = $("#report_format_id").val();
      let lang_code = $("#lang_code").val();

      if (submission) {
        $.ajax({
          url: site_url + "/option_server.php",
          type: "POST",
          data: {
            sign: "create_report_format_composed",
            report_format_id: report_format,
            content: json_report,
            lang_code: lang_code,
          },
        }).done(function (data) {
          if (data == "success") {
            alert($("#translation").data("user_js_phrase44"));
            //window.location.href = '?route=report_format_dcomposer&id=' + ticket + '&page=question';
            location.reload();
          } else alert(data);
        });
      }
    }
  );

  //Save report format composed
  $(".report-composer").on(
    "click",
    "#save_report_format_composed",
    function (event) {
      event.preventDefault();

      let report = {};
      let submission = true;

      //Front page
      let front_page_enable = $(
        "#composer_frontpage_heading .section-checker"
      ).is(":checked");
      let username = $("#composer_username").is(":checked");
      let company_name = $("#composer_company_name").is(":checked");
      let logo = $("#composer_company_logo").is(":checked");
      let ticket_id = $("#composer_ticket_id").is(":checked");
      let page_break1 = $("#composer_page_break1").is(":checked");
      report["front_page"] = {
        enabled: front_page_enable,
        username: username,
        company_name: company_name,
        logo: logo,
        ticket_id: ticket_id,
        page_break1: page_break1,
      };
      //Free Text 1
      let free_text1_enable = $(
        "#composer_freetext1_heading .section-checker"
      ).is(":checked");
      let free_text1 = tinymce.editors["composer_text_1"].getContent();
      let page_break2 = $("#composer_page_break2").is(":checked");
      report["free_text1"] = {
        enabled: free_text1_enable,
        text: free_text1,
        page_break2: page_break2,
      };
      //Intro Text
      let intro_text_enable = $(
        "#composer_introtext_heading .section-checker"
      ).is(":checked");
      let intro_text = tinymce.editors["composer_text_2"].getContent();
      let page_break3 = $("#composer_page_break3").is(":checked");
      report["intro_text"] = {
        enabled: intro_text_enable,
        text: intro_text,
        page_break3: page_break3,
      };
      //Free Text 2
      let free_text2_enable = $(
        "#composer_freetext2_heading .section-checker"
      ).is(":checked");
      let free_text3 = tinymce.editors["composer_text_3"].getContent();
      let page_break4 = $("#composer_page_break4").is(":checked");
      report["free_text2"] = {
        enabled: free_text2_enable,
        text: free_text3,
        page_break4: page_break4,
      };
      //Conflict Text
      let conflict_enable = $("#composer_conflict_heading .section-checker").is(
        ":checked"
      );
      let conflict_text = tinymce.editors["composer_text_4"].getContent();
      let page_break5 = $("#composer_page_break5").is(":checked");
      report["conflict"] = {
        enabled: conflict_enable,
        text: conflict_text,
        page_break5: page_break5,
      };
      //Free Text 3
      let free_text3_enable = $(
        "#composer_freetext3_heading .section-checker"
      ).is(":checked");
      let free_text5 = tinymce.editors["composer_text_5"].getContent();
      let page_break6 = $("#composer_page_break6").is(":checked");
      report["free_text3"] = {
        enabled: free_text3_enable,
        text: free_text5,
        page_break6: page_break6,
      };
      //Summary
      let summary_enable = $("#composer_summary_heading .section-checker").is(
        ":checked"
      );
      let summary_text = $("#composer_summary_text").html();
      let page_break7 = $("#composer_page_break7").is(":checked");
      report["summary"] = {
        enabled: summary_enable,
        text: summary_text,
        page_break7: page_break7,
      };
      //Free Text 4
      let free_text4_enable = $(
        "#composer_freetext4_heading .section-checker"
      ).is(":checked");
      let free_text6a = tinymce.editors["composer_text_6a"].getContent();
      let free_text6b = tinymce.editors["composer_text_6b"].getContent();
      let page_break8 = $("#composer_page_break8").is(":checked");
      report["free_text4"] = {
        enabled: free_text4_enable,
        text: free_text6a,
        text2: free_text6b,
        page_break8: page_break8,
      };
      //Radar Graph
      let graph_enable = $("#composer_graph_heading .section-checker").is(
        ":checked"
      );
      let graph_header = $("#composer_radar_graph_header").val();
      // let graph1_ctn = $('#radar_graph_1').find('canvas');
      // let graph2_ctn = $('#radar_graph_2').find('canvas');
      // let graph1_canvas = document.getElementById(graph1_ctn.attr('id'));
      // let graph2_canvas = document.getElementById(graph2_ctn.attr('id'));
      let graph1 = "";
      let graph2 = "";
      // if (graph_enable) {
      //     if (graph1_ctn.length && graph2_ctn.length) {
      //         graph1 = graph1_canvas.toDataURL("image/jpeg");
      //         graph2 = graph2_canvas.toDataURL("image/jpeg");
      //     } else {
      //         alert($('#translation').data('user_js_phrase43'));
      //         submission = false;
      //     }
      // }
      let free_text8 = tinymce.editors["composer_text_8"].getContent();
      let page_break9 = $("#composer_page_break9").is(":checked");
      report["radar_graph"] = {
        enabled: graph_enable,
        header: graph_header,
        graph1: graph1,
        graph2: graph2,
        text: free_text8,
        page_break9: page_break9,
      };
      //Free Text 6
      let free_text6_enable = $(
        "#composer_freetext6_heading .section-checker"
      ).is(":checked");
      let free_text9 = tinymce.editors["composer_text_9"].getContent();
      let page_break10 = $("#composer_page_break10").is(":checked");
      report["free_text6"] = {
        enabled: free_text6_enable,
        text: free_text9,
        page_break10: page_break10,
      };

      //Review
      let review_enable = $("#chk_review_check").is(":checked");
      let page_break_review = $("#composer_page_break_review").is(":checked");
      report["review"] = {
        enabled: review_enable,
        page_break_review: page_break_review,
      };

      //Rating
      let rating_enable = $("#chk_rating_check").is(":checked");
      let page_break_rating = $("#composer_page_break_rating").is(":checked");
      report["rating"] = {
        enabled: rating_enable,
        page_break_rating: page_break_rating,
      };

      //Methods
      let method_enable = $("#composer_methods_heading .section-checker").is(
        ":checked"
      );
      let header_text = $(".composer-method-header-text").html();
      let methods = $("#ranked_methods").val();
      let page_break11 = $("#composer_page_break11").is(":checked");
      report["method"] = {
        enabled: method_enable,
        header_text: header_text,
        methods: methods,
        page_break11: page_break11,
      };
      //Free Text 7
      let free_text7_enable = $(
        "#composer_freetext7_heading .section-checker"
      ).is(":checked");
      let free_text10 = tinymce.editors["composer_text_10"].getContent();
      let page_break12 = $("#composer_page_break12").is(":checked");
      report["free_text7"] = {
        enabled: free_text7_enable,
        text: free_text10,
        page_break12: page_break12,
      };
      //Tips
      let tips_enable = $("#composer_tips_heading .section-checker").is(
        ":checked"
      );
      let tips = $("#question_tips").val();
      let page_break13 = $("#composer_page_break13").is(":checked");
      report["tip"] = {
        enabled: tips_enable,
        tips: tips,
        page_break13: page_break13,
      };
      //Free Text 8
      let free_text8_enable = $(
        "#composer_freetext8_heading .section-checker"
      ).is(":checked");
      let free_text11 = tinymce.editors["composer_text_11"].getContent();
      let page_break14 = $("#composer_page_break14").is(":checked");
      report["free_text8"] = {
        enabled: free_text8_enable,
        text: free_text11,
        page_break14: page_break14,
      };
      //Disclaimer
      let disclaimer_enable = $(
        "#composer_disclaimer_heading .section-checker"
      ).is(":checked");
      let disclaimer_text = tinymce.editors["composer_text_12"].getContent();
      let free_text13 = tinymce.editors["composer_text_13"].getContent();
      let page_break15 = $("#composer_page_break15").is(":checked");
      report["disclaimer"] = {
        enabled: disclaimer_enable,
        disclaimer_text: disclaimer_text,
        free_text: free_text13,
        page_break15: page_break15,
      };
      //Assesment
      let assessment_enable = $(
        "#composer_assessment_heading .section-checker"
      ).is(":checked");

      let risks = {};
      let risksRumours = [];
      $(".risk_type_rumours").each(function () {
        risksRumours.push($(this).html());
      });
      risks["rumours"] = risksRumours;

      let risksNotEnoughInfo = [];
      $(".risk_type_not_enough_info").each(function () {
        risksNotEnoughInfo.push($(this).html());
      });
      risks["notEnoughInfo"] = risksNotEnoughInfo;

      let risksInaccessible = [];
      $(".risk_type_inaccessible_leader").each(function () {
        risksInaccessible.push($(this).html());
      });
      risks["inaccessibleLeader"] = risksInaccessible;

      let risksSickleave = [];
      $(".risk_type_sickleave").each(function () {
        risksSickleave.push($(this).html());
      });
      risks["sickleave"] = risksSickleave;

      let risksPolarization = [];
      $(".risk_type_polarization").each(function () {
        risksPolarization.push($(this).html());
      });
      risks["polarization"] = risksPolarization;

      report["assessment"] = {
        enabled: assessment_enable,
        risks: risks,
      };

      json_report = JSON.stringify(report);

      let report_format = $("#report_format_id").val();
      let lang_code = $("#lang_code").val();

      if (submission) {
        $.ajax({
          url: site_url + "/option_server.php",
          type: "POST",
          data: {
            sign: "create_report_format_composed",
            report_format_id: report_format,
            content: json_report,
            lang_code: lang_code,
          },
        }).done(function (data) {
          if (data == "success") {
            alert($("#translation").data("user_js_phrase44"));
            window.location.href =
              "?route=ticket&id=" + ticket + "&page=question";
          } else alert(data);
        });
      }
    }
  );

  //Terms and Conditions
  //Question tips, TinyMCE init
  var MCEConfig = {
    selector: ".tos-editor",
    height: 350,
    content_style:
      "@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');",
    font_formats:
      "Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Montserrat=Montserrat, sans-serif;Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats;",
    plugins: [
      "advlist anchor autolink codesample fullscreen help image imagetools",
      " lists link media noneditable preview",
      " searchreplace table template visualblocks wordcount",
    ],
    toolbar:
      "insertfile a11ycheck undo redo | bold italic | forecolor backcolor | codesample | alignleft aligncenter alignright alignjustify | bullist numlist | link image",
    spellchecker_dialog: true,
  };
  tinymce.init(MCEConfig);

  //Change tos company
  $("#tos_selection").change(function () {
    let company_id = $(this).val();
    if (company_id.length > 0) {
      window.location.href =
        site_url + "/user/index.php?route=tos&company=" + company_id;
    } else {
      window.location.href = site_url + "/user/index.php?route=tos";
    }
  });

  $("#accept-tc").click(function (event) {
    event.preventDefault();
    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "notification_update",
      },
    }).done(function (data) {
      if (data == "success") {
        $("#divNotiPopup").hide();
      } else {
        alert("Pease contact administrator.");
      }
    });
  });
  $("#close-tc").click(function (event) {
    event.preventDefault();
    $("#divNotiPopup").hide();
  });

  $(".tos_save").click(function (event) {
    event.preventDefault();
    let editor = $(this).parents(".tos-editor-ctn").find(".tos-editor");
    let tos_content = tinymce.editors[editor.attr("id")].getContent();
    let company_id = $(this).data("company_id");
    let lang_code = $(this).data("lang_code");
    let isNotify = $('input[name="chkNotifyUser"]:checked').val();

    if (isNotify != 1) {
      isNotify = 0;
    }

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "tos_update",
        tos_content: tos_content,
        company_id: company_id,
        lang_code: lang_code,
        isNotify: isNotify,
      },
    }).done(function (data) {
      if (data == "success") {
        window.location.reload();
      } else alert(data);
    });
  });

  //Package actions
  //Show new package form
  $(".package-add-new").click(function () {
    let package_form = $(".package-editor-sample").clone(true);
    package_form.removeClass("package-editor-sample").addClass("package-new");
    $(".package-editor-ctn div").remove();
    $(".package-editor-ctn").append(package_form);
    $(this).css("display", "none");
  });
  //Show edit package form
  $(".package-edit-btn").click(function () {
    let package_form = $(".package-editor-sample").clone(true);
    let package_info_input = $(this).parents("tr").find(".package-info input");
    let package_info = JSON.parse(package_info_input.val());

    package_form.removeClass("package-editor-sample").addClass("package-edit");
    package_form.data("package_id", $(this).data("package_id"));
    package_form
      .find(".package-new-title")
      .text($("#translation").data("user_js_phrase31"));
    package_form.find("#package_new_name").val(package_info["package_name"]);
    package_form.find("#package_new_price").val(package_info["package_price"]);
    package_form.find("#package_new_user").val(package_info["package_user"]);
    package_form
      .find("#package_new_size_min")
      .val(package_info["package_size_min"]);
    package_form
      .find("#package_new_size_max")
      .val(package_info["package_size_max"]);
    package_form
      .find("#package_new_details")
      .val(package_info["package_details"]);
    package_form
      .find("#package_new_submit")
      .data("package_id", $(this).data("package_id"));

    $(".package-editor-ctn div").remove();
    $(".package-editor-ctn").append(package_form);
    $(".package-add-new").css("display", "none");
  });
  //Close new/edit package form
  $(".package-editor-ctn").on("click", "#package_new_cancel", function () {
    $(".package-editor-ctn div").remove();
    $(".package-add-new").css("display", "inline");
  });
  //Package data retrieve base on language
  $(".package-editor-ctn").on("change", "#package_new_lang", function () {
    if ($(this).parents(".package-edit").length) {
      let parent = $(this).parents(".package-edit");
      let package_id = parent.data("package_id");
      let language_code = $(this).val();

      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "retrieve_package_content",
          package_id: package_id,
          lang_code: language_code,
        },
      }).done(function (data) {
        let package_info = JSON.parse(data);
        parent.find("#package_new_name").val(package_info["package_name"]);
        parent.find("#package_new_price").val(package_info["package_price"]);
        parent.find("#package_new_user").val(package_info["package_user"]);
        parent
          .find("#package_new_size_min")
          .val(package_info["package_size_min"]);
        parent
          .find("#package_new_size_max")
          .val(package_info["package_size_max"]);
        parent
          .find("#package_new_details")
          .val(package_info["package_details"]);
      });
    }
  });
  //Add/edit package
  $(".package-editor-ctn").on("click", "#package_new_submit", function (event) {
    event.preventDefault();
    let package_editor = $(".package-editor-ctn");

    let lang_input = package_editor.find("#package_new_lang");
    let name_input = package_editor.find("#package_new_name");
    let price_input = package_editor.find("#package_new_price");
    let user_input = package_editor.find("#package_new_user");
    let size_min_input = package_editor.find("#package_new_size_min");
    let size_max_input = package_editor.find("#package_new_size_max");
    let details_input = package_editor.find("#package_new_details");

    let lang = lang_input.val();
    let name = name_input.val();
    let price = parseFloat(price_input.val());
    let user = parseInt(user_input.val());
    let size_min = parseInt(size_min_input.val());
    let size_max = parseInt(size_max_input.val());
    let details = details_input.val();
    let package_edit = 0;

    //Package edit
    if ($(this).data("package_id")) {
      package_edit = $(this).data("package_id");
    }

    let validation = true;
    if (name.length < 1) {
      name_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }
    if (!price || price <= 0) {
      price_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }
    if (!user || user <= 1) {
      user_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }
    if (!size_min || size_min < 1 || size_min > size_max) {
      size_min_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }
    if (!size_max || size_max < 1 || size_max < size_min) {
      size_max_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      validation = validation & false;
    }

    if (validation) {
      name_input.css("border-color", "#ced4da");
      price_input.css("border-color", "#ced4da");
      user_input.css("border-color", "#ced4da");
      size_min_input.css("border-color", "#ced4da");
      size_max_input.css("border-color", "#ced4da");

      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "add_package",
          package_lang: lang,
          package_name: name,
          package_price: price,
          package_user: user,
          package_size_min: size_min,
          package_size_max: size_max,
          package_details: details,
          package_edit: package_edit,
        },
      }).done(function (data) {
        if (data == "success") window.location.reload();
        else alert(data);
      });
    }
  });
  //Delete package
  $(".package-delete").click(function (event) {
    event.preventDefault();

    let package_id = $(this).data("package_id");

    let confirm_delete = confirm($("#translation").data("user_js_phrase23"));
    if (confirm_delete) {
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: { sign: "delete_package", package_id: package_id },
      }).done(function (data) {
        if (data == "success") window.location.reload();
        else alert(data);
      });
    }
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

  $(".copyReponderLink").click(function () {
    var link = $(this).data("link");

    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(link).select();
    document.execCommand("copy");
    $temp.remove();

    $(this).find(".copyIcon").first().addClass("d-none");
    $(this).find(".copiedIcon").first().removeClass("d-none");

    $(this).tooltip({
      title: "Copied",
      placement: "right",
      trigger: "manual",
    });
    $(this).tooltip("show");

    setTimeout(function () {
      $(".tooltip").remove();
      $(".copyReponderLink")
        .removeAttr("data-bs-original-title")
        .removeAttr("title")
        .removeAttr("aria-describedby");

      $(".copyReponderLink .copiedIcon").addClass("d-none");
      $(".copyReponderLink .copyIcon").removeClass("d-none");
    }, 3000);
  });

  $('[data-toggle="tooltip"]').tooltip();

  //Clear all tracking
  $("#clear_tracking").click(function () {
    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: { sign: "clear_tracking" },
    }).done(function (data) {
      if (data == "success") window.location.reload();
      else alert(data);
    });
  });

  $("#company_role_select").change(function () {
    let company_id = $(this).data("company");
    var selected_role = $("#company_role_select option:selected").val();
    console.log(selected_role);

    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "company_role",
        role: selected_role,
        company_id: company_id,
      },
    }).done(function (data) {
      if (data == "success") window.location.reload();
      else alert(data);
    });
  });
});

function toggleBackgroundColor(card) {
  const isCardOpened = card.classList.contains("opened");

  if (isCardOpened) {
    card.style.backgroundColor = "#fff";
    card.classList.remove("opened");
  } else {
    card.style.backgroundColor = "#f0f0f0";
    card.classList.add("opened");
  }
}

const cards = document.querySelectorAll(".card.hover-effect");

cards.forEach(function (card) {
  card.addEventListener("click", function () {
    toggleBackgroundColor(this);
  });

  if (!card.classList.contains("opened")) {
    card.addEventListener("mouseenter", function () {
      card.style.backgroundColor = "#f0f0f0";
    });

    card.addEventListener("mouseleave", function () {
      card.style.backgroundColor = "#fff";
    });
  }
});
function getRandomHexColor() {
  return "#" + Math.floor(Math.random() * 16777215).toString(16);
}
function rangeToArray(start, end) {
  return Array.from({ length: end - start + 1 }, (_, index) => start + index);
}
function draw_radar_graph_res(context, heading) {
  let user_questions = JSON.parse($("#user_question_input").val()); //questions fra databse
  let res_questions = JSON.parse($("#res_question_input").val()); //questions fra databse
  let responsesData = JSON.parse($("#response_input").val()); //ticket_response fra database
console.log(context);
 
  let graph_category = [];
  let graph_label = [];
  let graph_score = [];
  let graph_score_data = [];
  let chartData = [];
  $(context)
    .parents(".radar-graph")
    .find(".graph-label")
    .each(function () {
      if ($(this).is(":checked")) {
        graph_category.push($(this).data("category"));
        graph_label.push($(this).val());
      }
    });

  let z = 1;
  for (var key in responsesData) {
    let categ = key;
    let responses = responsesData[key];

    // console.log(responses);

    for (let x = 0; x < graph_category.length; x++) {
      let score = 0;
      let found = 0;
      for (let y = 0; y < user_questions.length; y++) {
        let question_id = user_questions[y]["question_id"];
        const weight_yes = user_questions[y]["question_weight_yes"];
        const weight_no = user_questions[y]["question_weight_no"];

        if (user_questions[y]["category_id"] == graph_category[x]) {
          // console.log('qus_type : '+responses[question_id]['type']+', mcq :' + user_questions[y]['question_id'] + ', weight_yes :' + weight_yes + ', answer : ' + parseInt(responses[question_id]['answer']));
          //Main Question
          if (responses[question_id]["type"] == "mcq") {
            if (weight_yes == 1) {
              let value = parseInt(responses[question_id]["answer"]);
              if (value == 5) {
                value = 1;
              } else if (value == 4) {
                value = 2;
              } else if (value == 3) {
                value = 3;
              } else if (value == 2) {
                value = 4;
              } else if (value == 1) {
                value = 5;
              } else if (value == 6) {
                value = 0;
              }
              score += value;
            } else if (weight_yes == 5) {
              let value = parseInt(responses[question_id]["answer"]);
              if (value == 5) {
                value = 5;
              } else if (value == 4) {
                value = 4;
              } else if (value == 3) {
                value = 3;
              } else if (value == 2) {
                value = 2;
              } else if (value == 1) {
                value = 1;
              } else if (value == 6) {
                value = 0;
              }
              score += value;
            } else {
              if (parseInt(responses[question_id]["answer"]) == 6) {
                score += parseInt(responses[question_id]["answer"]);
              } else {
                score += parseInt(responses[question_id]["answer"]);
              }
              // console.log(graph_category[x] + ' : ' +parseInt(responses[question_id]['answer']));
            }
          }
          found++;
        }
      }
      // console.log('score ' + score);
      // console.log('found ' + found);
      let avg_score = score / found;

      graph_score[x] = avg_score.toFixed(2);
    }

    var chartPushData = { categ: categ, data: graph_score };
    graph_score_data.push(chartPushData);
    graph_score = [];

    z++;
  }

  let i = 1;
  for (let i = 0; i < graph_score_data.length; i++) {
    let colorHex = getRandomHexColor();



    function areAllValuesZero(graph_score_data) {
      // Check if graph_score_data is undefined or null
      if (graph_score_data === undefined || graph_score_data === null) {
        return false; // Or handle this case as needed
      }
    
      // Loop through graph_score_data
      for (let i = 0; i < graph_score_data.length; i++) {
        const data = graph_score_data[i]?.data; // Use optional chaining to safely access data property
        if (data === undefined) {
          return false;
        }
    
        // Loop through data array
        for (let v = 0; v < data.length; v++) {
          if (data[v] !== '0.00') {
            
            return false;
          }
        }
      }
      return true;
    }

    const responsetrue = areAllValuesZero(graph_score_data);
    if (responsetrue) {
      continue;
    }
    





    let chartDataSet = {
      label: graph_score_data[i]["categ"],
      fill: colorHex,
      backgroundColor: colorHex,
      borderColor: colorHex,
      pointBorderColor: colorHex,
      pointBackgroundColor: colorHex,
      data: graph_score_data[i]["data"],
    };

    chartData.push(chartDataSet);
  }
  console.log(chartData);
// ----------- GridLine Color
  //TODO: need to remap so that only under the value 2.5 is red, here lower value can vary
  const gridColor = rangeToArray(0,40).map((e,i)=>i==5?"green":Chart.defaults.borderColor)
  const lineWidth = rangeToArray(0,40).map((e,i)=>i==5?2:1)
  console.log(gridColor)
  // Draw Graph
  let canvas = document.createElement("canvas");
  canvas.id = "graph_res";
  canvas.className = "radar_graph_canvas";
  canvas.width = 700;
  canvas.height = 700;
  const grapharea = canvas.getContext("2d");
  // let colorHex = getRandomHexColor();
  chart_1_ = new Chart(grapharea, {
    type: "radar",
    data: {
      labels: graph_label,
      datasets: chartData,
    },
    plugins: [customCanvasBackgroundColor],
    options: {
      animation: { duration: 0 },
      plugins: {
        customCanvasBackgroundColor: {
          color: "white",
        },
      },
      title: {
        display: true,
        text: heading,
      },
      elements: {
        line: {
          borderWidth: 5,
        },
      },
      stroke: {
        width: 3,
      },
      scales: {
        r:{
          angleLines: {
            display: false,
          },
          min: 0,      // Set the minimum value to 0
    max: 5,      // Set the maximum value to 5
    stepSize: 1, // Set the step size to 1
    beginAtZero: false,
          // min: 1.0,  // Set the minimum value to 1.0
          // max: 5.0,  // Set the maximum value to 5.0
          // stepSize: 1.0,  // Set the step size to 1.0
          // beginAtZero: false,
          // ticks: {
          //   min: 0,
          //   max: 5.0,
          //   // suggestedMax: 5.0,
          // },
          // ticks: {
          //   beginAtZero: true, // Start the scale at 0
          //   suggestedMin: 0,   // Suggested minimum value
          //   suggestedMax: 5.0, // Suggested maximum value
          //   stepSize: 1.0,     // Set the step size to 1.0
          // },
          pointLabels: {
            fontSize: 12, // font size in pixels
            fontStyle: "bold",
          },
          grid:{
            color: gridColor,
            lineWidth: lineWidth
          }
        }
      },
      responsive: true,
    },
  });

  $(context).parents(".radar-graph").find("canvas").remove();
  $(context).parents(".radar-graph").append(canvas);
  
}


// table graph
async function createDataTable() {
$('.ques-loader').show();
  let user_questions = JSON.parse($("#user_question_input_table").val()); // Questions from the database
  let responsesData = JSON.parse($("#response_input_table").val()); // Ticket responses from the database

  let graph_category = [];
  let tableData = [];

  $(".graph-label-table:checked").each(function () {
    if ($(this).is(":checked")) {
      graph_category.push($(this).data("category"));
    }
  });


  console.log(user_questions);
  for (let responder in responsesData) {
    for (let question_id in responsesData[responder]) {
      let response = responsesData[responder][question_id];
      let found = 0;

      for (let x = 0; x < graph_category.length; x++) {
        for (let i = 0; i < user_questions.length; i++) {
          let user_question = user_questions[i];

          if (
            user_question["category_id"] == graph_category[x] &&
            user_question["question_id"] == question_id
          ) {
            if (response && response["type"] == "mcq") {
              const weight_yes = user_question["question_weight_yes"];
              const weight_no = user_question["question_weight_no"];
              let value = parseInt(response["answer"]);
              let score = 0;

              if (weight_yes == 1) {
                // Adjust score based on weight
                if (value == 5) value = 1;
                else if (value == 4) value = 2;
                else if (value == 3) value = 3;
                else if (value == 2) value = 4;
                else if (value == 1) value = 5;
                else if (value == 6) value = 0;
                score += value;
              } else if (weight_yes == 5) {
                // Adjust score based on weight
                if (value == 5) value = 5;
                else if (value == 4) value = 4;
                else if (value == 3) value = 3;
                else if (value == 2) value = 2;
                else if (value == 1) value = 1;
                else if (value == 6) value = 0;
                score += value;
              } else {
                if (parseInt(response["answer"]) == 6) {
                  score += parseInt(response["answer"]);
                } else {
                  score += parseInt(response["answer"]);
                }
              }
              found++;

              let avg_score = found > 0 ? score / found : 0; // Check for division by zero

              let scoreValueBg;

              if (avg_score >= 1.0 && avg_score <= 2.4) {
                scoreValueBg = "lightblue";
              } else if (avg_score === 2.5) {
                scoreValueBg = "Medgreenium";
              } else if (avg_score >= 2.6 && avg_score <= 3.9) {
                scoreValueBg = "yellow";
              } else if (avg_score >= 4.0 && avg_score <= 4.9) {
                scoreValueBg = "orange";
              } else if (avg_score >= 5.0) {
                scoreValueBg = "red";
              } else {
                scoreValueBg = "gray";
              }
              async function fetchQuestionAndCategory(user_question) {
                return new Promise((resolve, reject) => {
                  $.ajax({
                    url: site_url + 'get_ques_cat.php',
                    data: {
                      ques_id: user_question["question_id"],
                      cat_id: user_question["category_id"],
                      type:responder,
                    },
                    dataType: 'json',
                    success: function (response) {
                      // 'response' now contains the JSON data
                      resolve(response);
                    },
                    error: function (error) {
                      reject(error);
                    },
                  });
                });
              }
              
              async function getQuestionAndCategory(user_question) {
                try {
                  const response = await fetchQuestionAndCategory(user_question);
              
                  if (response && response.category && response.question) {
                    return {
                      category: response.category,
                      question: response.question,
                    };
                  } else {
                    throw new Error('Invalid response format');
                  }
                } catch (error) {
                  throw error;
                }
              }
              
              try {
                const { category, question } = await fetchQuestionAndCategory(user_question);
                  

                tableData.push({
                  Category: category,
                  Description: question,
                  resp:responder,
                  quesid:user_question['question_id'],
                  Question: user_question["question_text"],
                  ScoreValue: avg_score.toFixed(2),
                  ScoreBg: scoreValueBg,
                });
  
              } catch (error) {
                console.error(error);
              }
            }
          }
        }
      }
    }
  }

  let table = '<table border="1" id="gtable" class="table-bordered" style="width: 100%; border-collapse: collapse;"><thead><tr style="border-bottom: 1px solid black;"><th width="50px">Question</th> <th width="390px">Description</th><th width="130px">Category</th><th width="75px">User/Responder</th><th width="55px">Score</th></tr></thead>';

  let tablecheckboxes = '<tr style="border-bottom: 1px solid black;"><th width="100px" style="border: 1px solid #dee2e6;font-weight: 500;">Action</th></tr>';

  table += `<tbody>`;
  var ct = 1;
  tableData.forEach((row) => {
    table += `<tr class="${row.resp}" height="70px" border="1" id="table_row_${ct}" style="border: 1px solid #f1e4e4;font-size: 12px;"><td>${row.quesid}</td><td>${row.Description}</td><td>${row.Category}</td><td>${row.resp}</td><td style='background-color:${row.ScoreBg};'>${row.ScoreValue}</td></tr>`;

    tablecheckboxes += `<tr class="${row.resp}" height="70px" style="border: 1px solid #f1e4e4;"><td style="border: 1px solid #f1e4e4;" ><input type='checkbox' name='checkbox_row' class='check_box_row' style='' id='${ct}'></td></tr>`;
    ct++; 
  });
  table += `</tbody>`;
  table += '</table>';

  // alert(table);

  $(".tableContainer").html(table);
  $(".checkboxContainer").html(tablecheckboxes);
  $('.ques-loader').hide();
  initializeDataTable();
  // 
  // categories table
  const userCategoryMap = {};

  // Loop through the data and collect categories for each user
  tableData.forEach((row) => {
    const user = row.resp;
    const category = row.Category;
  
    // Check if the user is already in the dictionary
    if (!userCategoryMap[user]) {
      userCategoryMap[user] = new Set(); // Use a Set to store unique categories for each user
    }
  
    userCategoryMap[user].add(category); // Add the category to the user's Set
  });
  
  // Updated category table HTML generation
  let cat_table = '<table border="1" id="cctable" class="table-bordered" style="width: 100%; border-collapse: collapse;"><thead><tr style="border-bottom: 1px solid black;"><th width="10px">Serial No</th><th width="370px">Categories</th><th width="30px">User/Responder</th><th width="55px">Score Value</th></tr></thead>';
  
  let cat_tablecheckboxes = '<tr style="border-bottom: 1px solid black;"><th width="100px" style="border: 1px solid #dee2e6;font-weight: 500;">Action</th></tr>';
  
  cat_table += `<tbody>`;
  var ctg = 1;
  
  // Iterate through unique users and their categories
  for (const user in userCategoryMap) {
    const categories = Array.from(userCategoryMap[user]).join(', '); // Convert Set to array and join
  
    // Display each user and their categories
    cat_table += `<tr class="${user}" height="70px" border="1" id="ccattable_row_${ctg}" style="border: 1px solid #f1e4e4;"><td>${ctg}</td><td>${categories}</td><td>${user}</td><td style='background-color:${tableData[ctg - 1].ScoreBg || ''};'>${tableData[ctg - 1].ScoreValue}</td></tr>`; // Access 'ScoreBg' from tableData
  
    cat_tablecheckboxes += `<tr class="${user}" height="88px" style="border: 1px solid #f1e4e4;"><td style="border: 1px solid #f1e4e4;" ><input type='checkbox' name='checkbox_row' class='check_box_row_company_categories' style='' id='${ctg}'></td></tr>`;
    ctg++;
  }
  
  cat_table += `</tbody>`;
  cat_table += '</table>';
  
  $(".tableCompanyCategoriesContainer").html(cat_table);
  $(".checkboxcompanyCategoryContainer").html(cat_tablecheckboxes);
  $('.ques-loader-company-categories').hide();
  initializeCompanyCategoryDataTable();


// 
}


// categorytable
async function createCategoriesDataTable() {
  $('.ques-loader').show();
  let user_questions = JSON.parse($("#user_question_input_table").val()); // Questions from the database
  let responsesData = JSON.parse($("#response_input_table").val()); // Ticket responses from the database

  let graph_category = [];
  let tableData = [];

  $(".graph-label-table:checked").each(function () {
    if ($(this).is(":checked")) {
      graph_category.push($(this).data("category"));
    }
  });

  // Function to fetch question and category data
  async function fetchQuestionAndCategory(user_question) {
    return new Promise((resolve, reject) => {
      $.ajax({
        url: site_url + 'get_ques_cat.php',
        data: {
          ques_id: user_question["question_id"],
          cat_id: user_question["category_id"],
        },
        dataType: 'json',
        success: function (response) {
          // 'response' now contains the JSON data
          resolve(response);
        },
        error: function (error) {
          reject(error);
        },
      });
    });
  }

  // Create a dictionary to collect user, category, and score totals and counts
  const userCategoryScore = {};

  // Loop through the data and collect scores for each user, category, and question
  for (let responder in responsesData) {
    for (let question_id in responsesData[responder]) {
      let response = responsesData[responder][question_id];

      for (let x = 0; x < graph_category.length; x++) {
        for (let i = 0; i < user_questions.length; i++) {
          let user_question = user_questions[i];

          if (
            user_question["category_id"] == graph_category[x] &&
            user_question["question_id"] == question_id
          ) {
            if (response && response["type"] == "mcq") {
              const weight_yes = user_question["question_weight_yes"];
              let value = parseInt(response["answer"]);
              let score = 0;

              if (weight_yes == 1) {
                // Adjust score based on weight
                if (value == 5) value = 1;
                else if (value == 4) value = 2;
                else if (value == 3) value = 3;
                else if (value == 2) value = 4;
                else if (value == 1) value = 5;
                else if (value == 6) value = 0;
                score += value;
              } else if (weight_yes == 5) {
                // Adjust score based on weight
                if (value == 5) value = 5;
                else if (value == 4) value = 4;
                else if (value == 3) value = 3;
                else if (value == 2) value = 2;
                else if (value == 1) value = 1;
                else if (value == 6) value = 0;
                score += value;
              } else {
                if (parseInt(response["answer"]) == 6) {
                  score += parseInt(response["answer"]);
                } else {
                  score += parseInt(response["answer"]);
                }
              }

              // Check if the user, category, and question combination exists in the dictionary
              const userCategoryQuestionKey = `${responder}-${graph_category[x]}`;
              if (!userCategoryScore[userCategoryQuestionKey]) {
                userCategoryScore[userCategoryQuestionKey] = {
                  scoreTotal: 0,
                  count: 0,
                  category_id: graph_category[x], // Store category ID
                  resp: responder, // Store user/responder
                };
              }

              // Update the score and count for this user, category, and question
              userCategoryScore[userCategoryQuestionKey].scoreTotal += score;
              userCategoryScore[userCategoryQuestionKey].count++;
            }
          }
        }
      }
    }
  }

  // Iterate through user, category combinations and calculate the average
  for (let userCategoryKey in userCategoryScore) {
    const { category_id, resp, scoreTotal, count } = userCategoryScore[userCategoryKey];
    const avg_score = count > 0 ? scoreTotal / count : 0;

    // Fetch the category name using the function
    const { category } = await fetchQuestionAndCategory({ question_id: 0, category_id: category_id });

    // Add the data to the tableData array
    tableData.push({
      CategoryName: category, // Category name
      CategoryID: category_id, // Category ID
      resp: resp, // User/Responder
      ScoreValue: avg_score.toFixed(2),
    });
  }

  let table = '<table border="1" id="ctable" class="table-bordered" style="width: 100%; border-collapse: collapse;"><thead><tr style="border-bottom: 1px solid black;"><th width="130px">Category No</th><th width="75px">Category Name</th><th width="75px">User/Responder</th><th width="55px">Score</th></tr></thead>';

  table += `<tbody>`;

  // 

  let tablecheckboxes = '<tr style="border-bottom: 1px solid black;"><th width="100px" style="border: 1px solid #dee2e6;font-weight: 500;">Action</th></tr>';
  var cct = 1;
  tableData.forEach((row) => {
    let scoreBgColor = getScoreBackgroundColor(row.ScoreValue);
    table += `<tr class="${row.resp}" height="70px" border="1" id="cattable_row_${cct}" style="border: 1px solid #f1e4e4;font-size: 12px;"><td>${row.CategoryID}</td><td>${row.CategoryName}</td><td>${row.resp}</td><td style='background:${scoreBgColor};'>${row.ScoreValue}</td></tr>`;
  
    // 
    tablecheckboxes += `<tr class="${row.resp}" height="70px" style="border: 1px solid #f1e4e4;"><td style="border: 1px solid #f1e4e4;" ><input type='checkbox' name='checkbox_row' class='check_box_row_categories' style='' id='${cct}'></td></tr>`;
    cct++; 
  });
  table += `</tbody>`;
  table += '</table>';

  $(".tableCategoriesContainer").html(table);
  $(".checkboxCategoryContainer").html(tablecheckboxes);
  $('.ques-loader-categories').hide();

  initializeCategoryDataTable();
}




// Function to determine background color based on score
function getScoreBackgroundColor(scoreValue) {
  if (scoreValue >= 1.0 && scoreValue <= 2.4) {
    return "lightblue";
  } else if (scoreValue === 2.5) {
    return "Medgreenium";
  } else if (scoreValue >= 2.6 && scoreValue <= 3.9) {
    return "yellow";
  } else if (scoreValue >= 4.0 && scoreValue <= 4.9) {
    return "orange";
  } else if (scoreValue >= 5.0) {
    return "red";
  } else {
    return "gray";
  }
}

// end caategory table









// 
// async function createDataTable() {
//   $('.ques-loader').show();
  
//   // Parse the data from the input fields
//   let user_questions = JSON.parse($("#user_question_input").val());
//   let responsesData = JSON.parse($("#response_input").val());

//   let graph_category = [];
//   let tableData = [];
// console.log(user_questions);
// console.log(responsesData);

//   $(".graph-label:checked").each(function () {
//     if ($(this).is(":checked")) {
//       graph_category.push($(this).data("category"));
//     }
//   });

  

//   for (let responder in responsesData) {
//     for (let question_id in responsesData[responder]) {
//       let response = responsesData[responder][question_id];
//       let found = 0;


//       for (let x = 0; x < graph_category.length; x++) {
//         for (let i = 0; i < user_questions.length; i++) {
//           let user_question = user_questions[i];

//           if (
//             user_question["category_id"] == graph_category[x] &&
//             user_question["question_id"] == question_id
//           ) {
//             if (response && response["type"] == "mcq") {
//               const weight_yes = user_question["question_weight_yes"];
//               const weight_no = user_question["question_weight_no"];
//               let value = parseInt(response["answer"]);
//               let score = 0;

//               if (weight_yes == 1) {
//                 // Adjust score based on weight
//                 if (value == 5) value = 1;
//                 else if (value == 4) value = 2;
//                 else if (value == 3) value = 3;
//                 else if (value == 2) value = 4;
//                 else if (value == 1) value = 5;
//                 else if (value == 6) value = 0;
//                 score += value;
//               } else if (weight_yes == 5) {
//                 // Adjust score based on weight
//                 if (value == 5) value = 5;
//                 else if (value == 4) value = 4;
//                 else if (value == 3) value = 3;
//                 else if (value == 2) value = 2;
//                 else if (value == 1) value = 1;
//                 else if (value == 6) value = 0;
//                 score += value;
//               } else {
//                 if (parseInt(response["answer"]) == 6) {
//                   score += parseInt(response["answer"]);
//                 } else {
//                   score += parseInt(response["answer"]);
//                 }
//               }
//               found++;

//               let avg_score = found > 0 ? score / found : 0;

//               let scoreValueBg;

//               if (avg_score >= 1.0 && avg_score <= 2.4) {
//                 scoreValueBg = "lightblue";
//               } else if (avg_score === 2.5) {
//                 scoreValueBg = "Medgreenium";
//               } else if (avg_score >= 2.6 && avg_score <= 3.9) {
//                 scoreValueBg = "yellow";
//               } else if (avg_score >= 4.0 && avg_score <= 4.9) {
//                 scoreValueBg = "orange";
//               } else if (avg_score >= 5.0) {
//                 scoreValueBg = "red";
//               } else {
//                 scoreValueBg = "gray";
//               }

//               try {
//                 const { category, question } = await getQuestionAndCategory(user_question);

//                 tableData.push({
//                   Category: category,
//                   Description: question,
//                   Question: user_question["question_text"],
//                   ScoreValue: avg_score.toFixed(2),
//                   ScoreBg: scoreValueBg,
//                 });
//               } catch (error) {
//                 console.error(error);
//               }
//             }
//           }
//         }
//       }
//     }
//   }

//   let tableHTML = '<table border="1" id="gtable" class="table-bordered" style="width: 100%; border-collapse: collapse;"><thead><tr style="border-bottom: 1px solid black;"><th width="100px">Question</th><th>Description</th><th width="200px">Category</th><th width="100px">Score Value</th></tr></thead>';
//   let checkboxHTML = '<tr style="border-bottom: 1px solid black;"><th width="100px" style="border: 1px solid #dee2e6;font-weight: 500;">Action</th></tr>';

//   let tableDataRows = [];
//   let checkboxRows = [];

//   tableData.forEach((row, ct) => {
//     tableDataRows.push(`<tr class='${}' height="70px" border="1" id="table_row_${ct + 1}" style="border: 1px solid #f1e4e4;"><td>${ct + 1}</td><td>${row.Description}</td><td>${row.Category}</td><td style='background-color:${row.ScoreBg};'>${row.ScoreValue}</td></tr>`);
//     checkboxRows.push(`<tr height="70px" style="border: 1px solid #f1e4e4;"><td style="border: 1px solid #f1e4e4;" ><input type='checkbox' name='checkbox_row' class='check_box_row' style='' id='${ct + 1}'></td></tr>`);
//   });

//   tableHTML += `<tbody>${tableDataRows.join('')}</tbody></table>`;
//   checkboxHTML += checkboxRows.join('');

//   $(".tableContainer").html(tableHTML);
//   $(".checkboxContainer").html(checkboxHTML);

//   $('.ques-loader').hide();
//   initializeDataTable();
// }
// Add the missing getQuestionAndCategory function here
// async function getQuestionAndCategory(user_question) {
//   return new Promise((resolve, reject) => {
//     $.ajax({
//       url: site_url + 'get_ques_cat.php',
//       data: {
//         ques_id: user_question["question_id"],
//         cat_id: user_question["category_id"],
//       },
//       dataType: 'json',
//       success: function (response) {
//         // 'response' now contains the JSON data
//         resolve(response);
//       },
//       error: function (error) {
//         reject(error);
//       },
//     });
//   });
// }

// 

// table graph

// end table graph data


function getRandomIntInclusive(min, max) {
  min = Math.ceil(min);
  max = Math.floor(max);
  return Math.floor(Math.random() * (max - min + 1) + min); // The maximum is inclusive and the minimum is inclusive
}
// function draw_doughnut_graph_res(context, heading) {
//   let user_questions = JSON.parse($("#user_question_input").val()); //questions fra databse
//   let res_questions = JSON.parse($("#res_question_input").val()); //questions fra databse
//   let responsesData = JSON.parse($("#response_input").val()); //ticket_response fra database

//   let graph_category = [];
//   let graph_label = [];
//   let graph_score = [];
//   let graph_score_data = [];
//   let chartData = [];
//   $(context)
//     .parents(".radar-graph")
//     .find(".graph-label")
//     .each(function () {
//       if ($(this).is(":checked")) {
//         graph_category.push($(this).data("category"));
//         graph_label.push($(this).val());
//       }
//     });

//   let z = 1;
//   for (var key in responsesData) {
//     let categ = key;
//     let responses = responsesData[key];

//     // console.log(responses);

//     for (let x = 0; x < graph_category.length; x++) {
//       let score = 0;
//       let found = 0;
//       for (let y = 0; y < user_questions.length; y++) {
//         let question_id = user_questions[y]["question_id"];
//         const weight_yes = user_questions[y]["question_weight_yes"];
//         const weight_no = user_questions[y]["question_weight_no"];

//         if (user_questions[y]["category_id"] == graph_category[x]) {
//           // console.log('qus_type : '+responses[question_id]['type']+', mcq :' + user_questions[y]['question_id'] + ', weight_yes :' + weight_yes + ', answer : ' + parseInt(responses[question_id]['answer']));
//           //Main Question
//           if (responses[question_id]["type"] == "mcq") {
//             if (weight_yes == 1) {
//               let value = parseInt(responses[question_id]["answer"]);
//               if (value == 5) {
//                 value = 1;
//               } else if (value == 4) {
//                 value = 2;
//               } else if (value == 3) {
//                 value = 3;
//               } else if (value == 2) {
//                 value = 4;
//               } else if (value == 1) {
//                 value = 5;
//               } else if (value == 6) {
//                 value = 0;
//               }
//               score += value;
//             } else if (weight_yes == 5) {
//               let value = parseInt(responses[question_id]["answer"]);
//               if (value == 5) {
//                 value = 5;
//               } else if (value == 4) {
//                 value = 4;
//               } else if (value == 3) {
//                 value = 3;
//               } else if (value == 2) {
//                 value = 2;
//               } else if (value == 1) {
//                 value = 1;
//               } else if (value == 6) {
//                 value = 0;
//               }
//               score += value;
//             } else {
//               if (parseInt(responses[question_id]["answer"]) == 6) {
//                 score += parseInt(responses[question_id]["answer"]);
//               } else {
//                 score += parseInt(responses[question_id]["answer"]);
//               }
//               // console.log(graph_category[x] + ' : ' +parseInt(responses[question_id]['answer']));
//             }
//           }

//           found++;
//         }
//       }
//       // console.log('score ' + score);
//       // console.log('found ' + found);
//       let avg_score = score / found;

//       graph_score[x] = avg_score.toFixed(2);
//     }

//     var chartPushData = { categ: categ, data: graph_score };
//     graph_score_data.push(chartPushData);
//     graph_score = [];

//     z++;
//   }

//   // ---------------------- Chart Configuration Object
//   const colorConst = [
//     "rgb(255, 211, 248)",
//     "rgb(189, 224, 255)",
//     "rgb(239, 71, 111)",
//     "rgb(255, 209, 102)",
//     "rgb(6, 214, 160)",
//     "rgb(255, 247, 175)",
//     "rgb(250, 175, 255)",
//     "rgb(255, 175, 204)",
//     "rgb(255, 175, 220)",
//     "rgb(255, 157, 157)",
//     "rgb(194, 143, 89)",
//     "rgb(220, 255, 175)",
//     "rgb(255, 105, 198)",
//     "rgb(92, 85, 159)",
//     "rgb(123, 233, 172)",
//   ];

//   let i = 1;
//   for (let i = 0; i < graph_score_data.length; i++) {
//     let colorHex = getRandomHexColor();

//     let chartDataSet = {
//       label: graph_score_data[i]["categ"],
//       fill: colorHex,
//       backgroundColor:
//         colorConst[getRandomIntInclusive(0, colorConst.length - 1)],
//       borderColor: "rgb(255, 194, 156)",
//       pointBorderColor: colorHex,
//       pointBackgroundColor: colorHex,
//       data: graph_score_data[i]["data"],
//     };

//     chartData.push(chartDataSet);
//   }

//   //Draw Graph
//   let canvas = document.createElement("canvas");
//   let graph_id = "graph_res";
//   canvas.id = graph_id;
//   canvas.className = "radar_graph_canvas";
//   canvas.width = 700;
//   canvas.height = 700;
//   var grapharea = canvas.getContext("2d");
//   // let colorHex = getRandomHexColor();
//   // ---------------------------------------------------------------------------
//   const generateLabelsDef =
//     Chart.controllers.doughnut.overrides.plugins.legend.labels.generateLabels;

//   const cfg = {
//     type: "doughnut",
//     data: {
//       labels: chartData.map((e) => e.label),
//       datasets: chartData.map((e) => ({
//         ...e,
//         data: e.data.map((v, i) => ({ name: graph_label[i], value: v })),
//       })),
//       parsing: {
//         key: "data.value",
//       },
//     },
//     plugins: [ChartDataLabels, customCanvasBackgroundColor],
//     options: {
//       cutout: "20%",
//       datasets: {
//         doughnut: {
//           borderWidth: 4,
//           hoverBorderWidth: 0,
//           hoverOffset: 0,
//         },
//       },
//       title: {
//         display: true,
//         text: heading,
//       },
//       plugins: {
//         customCanvasBackgroundColor: {
//           color: "white",
//         },
//         datalabels: {
//           formatter: function (value) {
//             return value.value;
//           },
//         },
//         legend: {
//           labels: {
//             generateLabels(chart) {
//               const labels = generateLabelsDef(chart);
//               return labels.map((e) => {
//                 const meta = chart.getDatasetMeta(e.index);
//                 return {
//                   ...e,
//                   fillStyle: meta._dataset.backgroundColor,
//                   strokeStyle: meta._dataset.backgroundColor,
//                   hidden: !meta.visible,
//                 };
//               });
//             },
//           },
//           onClick: (e, legendItem, legend) => {
//             const chart = legend.chart;
//             chart.getDatasetMeta(legendItem.index).hidden =
//               !chart.getDatasetMeta(legendItem.index).hidden;
//             chart.update();
//           },
//         },
//         tooltip: {
//           titleAlign: "center",
//           callbacks: {
//             label: function (ctx) {
//               return `${ctx.raw.name}: ${ctx.raw.value}`;
//             },
//             title: (ctx) => {
//               return ctx[0].dataset.label;
//             },
//           },
//         },
//       },
//     },
//   };
//   // ---------------------- Chart Object
//   chart_1_ = new Chart(grapharea, cfg);

//   $(context).parents(".radar-graph").find("canvas").remove();
//   $(context).parents(".radar-graph").append(canvas);
// }


// new

// function draw_doughnut_graph_res(context, heading) {
//   let user_questions = JSON.parse($("#user_question_input").val()); // Questions from the database
//   let res_questions = JSON.parse($("#res_question_input").val()); // Questions from the database
//   let responsesData = JSON.parse($("#response_input").val()); // Ticket responses from the database

//   let graph_category = [];
//   let graph_label = [];
//   let graph_score = [];
//   let graph_score_data = [];
//   let chartData = [];
//   $(context)
//     .parents(".radar-graph")
//     .find(".graph-label")
//     .each(function () {
//       if ($(this).is(":checked")) {
//         graph_category.push($(this).data("category"));
//         graph_label.push($(this).val());
//       }
//     });

//   let z = 1;
//   for (var key in responsesData) {
//     let categ = key;
//     let responses = responsesData[key];

//     for (let x = 0; x < graph_category.length; x++) {
//       let score = 0;
//       let found = 0;
//       for (let y = 0; y < user_questions.length; y++) {
//         let question_id = user_questions[y]["question_id"];
//         const weight_yes = user_questions[y]["question_weight_yes"];
//         const weight_no = user_questions[y]["question_weight_no"];

//         if (user_questions[y]["category_id"] == graph_category[x]) {
//           if (responses[question_id]["type"] == "mcq") {
//             if (weight_yes == 1) {
//               let value = parseInt(responses[question_id]["answer"]);
//               if (value == 5) {
//                 value = 1;
//               } else if (value == 4) {
//                 value = 2;
//               } else if (value == 3) {
//                 value = 3;
//               } else if (value == 2) {
//                 value = 4;
//               } else if (value == 1) {
//                 value = 5;
//               }
//               score += value;
//             } else {
//               score += parseInt(responses[question_id]["answer"]);
//             }
//           }

//           found++;
//         }
//       }
//       let avg_score = score / found;
      
//       graph_score[x] = avg_score.toFixed(2);
//     }

//     var chartPushData = { categ: categ, data: graph_score };
//     graph_score_data.push(chartPushData);
//     graph_score = [];

//     z++;
   
//   }

//   // Assign colors based on the score range for each data point individually
//   const colorScale = (score) => {
//     if (score >= 1.00 && score <= 2.40) return "lightblue";
//     if (score === 2.50) return "green";
//     if (score >= 2.60 && score <= 3.90) return "yellow";
//     if (score >= 4.00 && score <= 4.90) return "orange";
//     if (score >= 5.00) return "red";
   
//     return "gray"; // Default color for unexpected values
   
//   };

//   let i = 1;

//   for (let i = 0; i < graph_score_data.length; i++) {
//     let score = parseFloat(graph_score_data[i]["data"][0]);
//     let colorHex = colorScale(score);
//   console.log(graph_score_data);
//     let chartDataSet = {
//       label: graph_score_data[i]["categ"],
//       fill: colorHex,
//       backgroundColor: colorHex,
//       borderColor: "rgb(255, 194, 156)",
//       pointBorderColor: colorHex,
//       pointBackgroundColor: colorHex,
//       data: graph_score_data[i]["data"],
//     };

//     chartData.push(chartDataSet);
//   }

//   // Draw Graph
//   let canvas = document.createElement("canvas");
//   let graph_id = "graph_res";
//   canvas.id = graph_id;
//   canvas.className = "radar_graph_canvas";
//   canvas.width = 700;
//   canvas.height = 700;
//   var grapharea = canvas.getContext("2d");

//   const generateLabelsDef =
//     Chart.controllers.doughnut.overrides.plugins.legend.labels.generateLabels;

//   const cfg = {
//     type: "doughnut",
//     data: {
//       labels: chartData.map((e) => e.label),
//       datasets: chartData.map((e) => ({
//         ...e,
//         data: e.data.map((v, i) => ({ name: graph_label[i], value: v })),
//       })),
//       parsing: {
//         key: "data.value",
//       },
//     },
//     plugins: [ChartDataLabels, customCanvasBackgroundColor],
//     options: {
//       cutout: "20%",
//       datasets: {
//         doughnut: {
//           borderWidth: 4,
//           hoverBorderWidth: 0,
//           hoverOffset: 0,
//         },
//       },
//       title: {
//         display: true,
//         text: heading,
//       },
//       plugins: {
//         customCanvasBackgroundColor: {
//           color: "white",
//         },
//         datalabels: {
//           formatter: function (value) {
//             return value.value;
//           },
//         },
//         legend: {
//           labels: {
//             generateLabels(chart) {
//               const labels = generateLabelsDef(chart);
//               return labels.map((e) => {
//                 const meta = chart.getDatasetMeta(e.index);
//                 return {
//                   ...e,
//                   fillStyle: meta._dataset.backgroundColor,
//                   strokeStyle: meta._dataset.backgroundColor,
//                   hidden: !meta.visible,
//                 };
//               });
//             },
//           },
//           onClick: (e, legendItem, legend) => {
//             const chart = legend.chart;
//             chart.getDatasetMeta(legendItem.index).hidden =
//               !chart.getDatasetMeta(legendItem.index).hidden;
//             chart.update();
//           },
//         },
//         tooltip: {
//           titleAlign: "center",
//           callbacks: {
//             label: function (ctx) {
//               return `${ctx.raw.name}: ${ctx.raw.value}`;
//             },
//             title: (ctx) => {
//               return ctx[0].dataset.label;
//             },
//             afterLabel: (ctx) => {
//               return `Question: ${ctx.raw.question}`;
//             },
//           },
//         },
//       },
//     },
//   };

//   // Chart Object
//   chart_1_ = new Chart(grapharea, cfg);

//   $(context).parents(".radar-graph").find("canvas").remove();
//   $(context).parents(".radar-graph").append(canvas);
// }







function draw_doughnut_graph_res(context, heading) {
  let user_questions = JSON.parse($("#user_question_input").val()); // Questions from the database
  let res_questions = JSON.parse($("#res_question_input").val()); // Questions from the database
  let responsesData = JSON.parse($("#response_input").val()); // Ticket responses from the database

  // console.log('user questions'+user_questions);
  // console.log('res questions'+res_questions);
  // console.log('response Ddata'+responsesData);
  console.log('res questions: ' + JSON.stringify(res_questions));

  let graph_category = [];
  let graph_label = [];
  let graph_score_data = [];
  let chartData = [];
  $(context)
    .parents(".radar-graph")
    .find(".graph-label")
    .each(function () {
      if ($(this).is(":checked")) {
        graph_category.push($(this).data("category"));
        graph_label.push($(this).val());
      }
    });

  let z = 1;
  for (var key in responsesData) {
    let categ = key;
    let responses = responsesData[key];
    let graph_score = [];

    for (let x = 0; x < graph_category.length; x++) {
      let score = 0;
      let found = 0;
      for (let y = 0; y < user_questions.length; y++) {
        let question_id = user_questions[y]["question_id"];
        const weight_yes = user_questions[y]["question_weight_yes"];
        const weight_no = user_questions[y]["question_weight_no"];

        if (user_questions[y]["category_id"] == graph_category[x]) {
          if (responses[question_id]["type"] == "mcq") {
            if (weight_yes == 1) {
              let value = parseInt(responses[question_id]["answer"]);
              if (value == 5) {
                value = 1;
              } else if (value == 4) {
                value = 2;
              } else if (value == 3) {
                value = 3;
              } else if (value == 2) {
                value = 4;
              } else if (value == 1) {
                value = 5;
              }
              score += value;
            } else {
              score += parseInt(responses[question_id]["answer"]);
            }
          }

          found++;
        }
      }
      let avg_score = score / found;
      if (avg_score > 5.0) {
      avg_score = 5.0; // Cap the score at 5.0
}
      graph_score.push(avg_score.toFixed(2));
    }

    var chartPushData = { categ: categ, data: graph_score };
    graph_score_data.push(chartPushData);

    z++;
  }

  // generate colors based on the score
  const generateColors = (scores) => {
    const colors = [];
    for (let i = 0; i < scores.length; i++) {
      const score = parseFloat(scores[i]);
      if (score >= 1.0 && score <= 2.4) colors.push("lightblue");
      else if (score === 2.5) colors.push("green");
      else if (score >= 2.6 && score <= 3.9) colors.push("yellow");
      else if (score >= 4.0 && score <= 4.9) colors.push("orange");
      else if (score >= 5.0) colors.push("red");
      else colors.push("gray");
    }
    return colors;
  };

  let i = 1;
  for (let i = 0; i < graph_score_data.length; i++) {
    let scores = graph_score_data[i]["data"];
    let colors = generateColors(scores);

    let chartDataSet = {
      label: graph_score_data[i]["categ"],
      backgroundColor: colors,  // Use the colors generated by generateColors
      borderColor: "rgb(255, 194, 156)",
      pointBorderColor: colors,  // You can also apply it here if needed
      pointBackgroundColor: colors,  // And here if needed
      data: scores,
      question: graph_label[i], // Store question text for tooltip
    };

    chartData.push(chartDataSet);
  }

  // Draw Graph
  let canvas = document.createElement("canvas");
  let graph_id = "graph_res";
  canvas.id = graph_id;
  canvas.className = "radar_graph_canvas";
  canvas.width = 700;
  canvas.height = 700;
  var grapharea = canvas.getContext("2d");

  const generateLabelsDef =
    Chart.controllers.doughnut.overrides.plugins.legend.labels.generateLabels;
  const cfg = {
    type: "doughnut",
    data: {
      labels: chartData.map((e) => e.label),
      datasets: chartData.map((e) => ({
        ...e,
        data: e.data.map((v, i) => ({ name: graph_label[i], value: v })),
      })),
      parsing: {
        key: "data.value",
      },
    },
    
    plugins: [ChartDataLabels, customCanvasBackgroundColor],
    options: {
      cutout: "20%",
      datasets: {
        doughnut: {
          borderWidth: 4,
          hoverBorderWidth: 0,
          hoverOffset: 0,
        },
      },
      
      title: {
        display: true,
        text: heading,
      },
      plugins: {
        customCanvasBackgroundColor: {
          color: "white",
        },
        datalabels: {
          formatter: function (value) {
            return value.value;
          },
        },
        legend: {
          labels: {
            generateLabels(chart) {
              const labels = generateLabelsDef(chart);
              return labels.map((e) => {
                const meta = chart.getDatasetMeta(e.index);
                return {
                  ...e,
                  fillStyle: meta._dataset.backgroundColor,
                  strokeStyle: meta._dataset.backgroundColor,
                  hidden: !meta.visible,
                };
              });
            },
          },
          onClick: (e, legendItem, legend) => {
            const chart = legend.chart;
            chart.getDatasetMeta(legendItem.index).hidden =
              !chart.getDatasetMeta(legendItem.index).hidden;
            chart.update();
          },
        },
        tooltip: {
          titleAlign: "center",
          callbacks: {
            label: function (ctx) {
              return `${ctx.raw.name}: ${ctx.raw.value}`;
            },
            title: (ctx) => {
              return ctx[0].dataset.label;
            },
            afterLabel: (ctx) => {
              console.log(ctx);
              return `Question: ${ctx.raw.name}`;
              
              // if (ctx && ctx.raw) {
              //   const question = ctx.dataset.question;
              //   console.log(`Question: ${question}`);
                
              // } else {
              //   console.error("ctx or ctx.raw is undefined or missing.");
              // }
           },
          },
        },
      },
    },
  };
  console.log(graph_label);
  // Chart Object
  chart_1_ = new Chart(grapharea, cfg);

  $(context).parents(".radar-graph").find("canvas").remove();
  $(context).parents(".radar-graph").append(canvas);
}


// end new
function draw_radar_graph(context, line_label, graph_text) {
  console.log("draw_radar_graph");
  let questions = JSON.parse($("#question_input").val()); //questions fra databse
  let responses = JSON.parse($("#response_input").val()); //ticket_response fra database

  let graph_category = []; //Kategorier som er checked i html
  let graph_label = [];
  let graph_score = [];
  $(context)
    .parents(".radar-graph")
    .find(".graph-label")
    .each(function () {
      if ($(this).is(":checked")) {
        graph_category.push($(this).data("category"));
        graph_label.push($(this).val());
      }
    });

  console.log(graph_category);
  console.log("lukman_draw_radar_graph");
  console.log(responses);
  for (let x = 0; x < graph_category.length; x++) {
    let score = 0;
    let found = 0;
    for (let y = 0; y < questions.length; y++) {
      let question_id = questions[y]["question_id"];
      const weight_yes = questions[y]["question_weight_yes"];
      const weight_no = questions[y]["question_weight_no"];
      if (questions[y]["category_id"] == graph_category[x]) {
        //Main Question
        if (responses[question_id]["type"] == "mcq") {
          console.log(
            "mcq " +
              questions[y]["question_id"] +
              " " +
              weight_yes +
              " answer: " +
              parseInt(responses[question_id]["answer"])
          );
          if (weight_yes == 1) {
            let value = parseInt(responses[question_id]["answer"]);
            if (value == 5) {
              value = 1;
            } else if (value == 4) {
              value = 2;
            } else if (value == 2) {
              value = 4;
            } else if (value == 1) {
              value = 5;
            }
            score += value;
          } else {
            score += parseInt(responses[question_id]["answer"]);
          }
        } else if (responses[question_id]["type"] == "yes-no") {
          console.log(
            "yes-no " +
              questions[y]["question_id"] +
              " " +
              weight_yes +
              " answer: " +
              parseInt(responses[question_id]["answer"])
          );
          if (responses[question_id]["answer"] == 2) {
            score += parseInt(weight_yes);
          } else {
            score += parseInt(weight_no);
          }
        }

        //Follow-up Question
        if (questions[y]["question_follow_up"]) {
          let follow_up_id = "";

          //Get follow-up type
          let follow_up_type = "";
          if (
            responses[question_id]["type"] == "yes-no" &&
            responses[question_id]["answer"] == 1
          ) {
            follow_up_type = "no";
          } else if (
            responses[question_id]["type"] == "yes-no" &&
            responses[question_id]["answer"] == 2
          ) {
            follow_up_type = "yes";
          } else if (
            responses[question_id]["type"] == "mcq" &&
            (responses[question_id]["answer"] == 1 ||
              responses[question_id]["answer"] == 2)
          ) {
            follow_up_type = "no";
          } else if (
            responses[question_id]["type"] == "mcq" &&
            (responses[question_id]["answer"] == 4 ||
              responses[question_id]["answer"] == 5)
          ) {
            follow_up_type = "yes";
          } else if (
            responses[question_id]["type"] == "mcq" &&
            responses[question_id]["answer"] == 3
          ) {
            score += 3;
          }

          //Set follow-up id
          if (follow_up_type) {
            if (follow_up_type == "yes") {
              follow_up_id = questions[y]["question_yes_follow_up"];
            } else {
              follow_up_id = questions[y]["question_no_follow_up"];
            }
          }

          //Score if follow-up available
          if (follow_up_id) {
            const follow_up_question = questions.find(
              (question) => question.question_id === follow_up_id
            );
            const weight_yes_follow_up =
              follow_up_question["question_weight_yes"];
            const weight_no_follow_up =
              follow_up_question["question_weight_no"];
            if (responses[follow_up_id]["type"] == "mcq") {
              if (weight_yes_follow_up == 1) {
                let value = parseInt(responses[follow_up_id]["answer"]);
                if (value == 5) {
                  value = 1;
                } else if (value == 4) {
                  value = 2;
                } else if (value == 2) {
                  value = 4;
                } else if (value == 1) {
                  value = 5;
                }
                score += value;
              } else {
                score += parseInt(responses[follow_up_id]["answer"]);
              }
            } else if (responses[follow_up_id]["type"] == "yes-no") {
              if (responses[follow_up_id]["answer"] == 2) {
                score += parseInt(weight_yes_follow_up);
              } else {
                score += parseInt(weight_no_follow_up);
              }
            }
            console.log(
              "follow-up added. question_id:" +
                follow_up_question["question_id"] +
                " " +
                weight_yes +
                " answer: " +
                parseInt(responses[follow_up_id]["answer"])
            );
            found++;
          }
        }

        found++;
      }
    }
    console.log("score " + score);
    console.log("found " + found);
    let avg_score = score / found;
    //let graph_data = {"label":graph_label[x], "score":score, "responses":found}
    graph_score.push(avg_score);
  }

  //Draw Graph
  let canvas = document.createElement("canvas");
  let graph_id = "graph_" + generate_string(5);
  canvas.id = graph_id;
  canvas.className = "radar_graph_canvas";
  canvas.width = 700;
  canvas.height = 700;
  var grapharea = canvas.getContext("2d");
  var chart_1 = new Chart(grapharea, {
    type: "radar",
    data: {
      labels: graph_label,
      datasets: [
        {
          label: line_label,
          fill: "rgba(255,99,132,0.2)",
          backgroundColor: "rgba(255,99,132,0.2)",
          borderColor: "#4F81BD",
          pointBorderColor: "#fff",
          pointBackgroundColor: "#4F81BD",
          data: graph_score,
        },
      ],
    },
    plugins: [customCanvasBackgroundColor],
    options: {
      plugins: {
        customCanvasBackgroundColor: {
          color: "white",
        },
      },
      title: {
        display: true,
        text: graph_text,
      },
      elements: {
        line: {
          borderWidth: 3,
        },
      },
      stroke: {
        width: 3,
      },
      scale: {
        angleLines: {
          display: false,
        },
        ticks: {
          suggestedMin: 0,
          suggestedMax: 3,
        },
      },
      responsive: true,
    },
  });

  // $(context).parents(".radar-graph").find("canvas").remove();
  // $(context).parents(".radar-graph").append(canvas);
}

function generate_string(length) {
  var result = "";
  var characters =
    "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  var charactersLength = characters.length;
  for (var i = 0; i < length; i++) {
    result += characters.charAt(Math.floor(Math.random() * charactersLength));
  }
  return result;
}

function validateEmail(email) {
  var re =
    /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(email);
}

function setProgBar(questionNo) {
  let progressbar = $("#progressbar");
  let questionIds = $("#questionIds").val();
  let answerIds = $("#answerIds").val();
  let pageNumber = $("#pageNumber").val();

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

  percent = Math.floor((numberOfAnswer * 100) / numberOfQuestion);

  progressbar.css({
    width: percent + "%",
  });

  $("#label-progressbar").html(
    percent + "% (" + numberOfAnswer + "/" + numberOfQuestion + ")"
  );
}

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

//last navigation buttton ticket
function saveAndNavToReview(ticketId, ticketStatus) {
  if (ticketStatus == "closed") {
    window.location.href =
      site_url + "/user/index.php?route=ticket&id=" + ticketId + "&page=review";
  } else {
    let ticket_name_input = $("#ticket_name");
    let ticket_name = ticket_name_input.val();
    let response = {};
    let ticket_id = ticketId;

    let validation = true;
    if (ticket_name.length < 1) {
      validation = validation & false;
      ticket_name_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
      window.scrollTo(0, 0);
    } else {
      ticket_name_input.css("border-color", "#ced4da");
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

      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "save_ticket",
          ticket_name: ticket_name,
          response: response,
          ticket_id: ticket_id,
        },
      }).done(function (data) {
        console.log(data);
        data = JSON.parse(data);
        if (data["status"] == "success") {
          window.location.href =
            site_url +
            "/user/index.php?route=ticket&id=" +
            data["ticket_id"] +
            "&page=review";
        } else {
          alert(data["message"]);
        }
      });
    }
  }
}

//delete company
function showDeleteCompanyModal(companyId, lblClose, lblDelete) {
  var htmlText = '<div class="modal-content">';
  htmlText += '<div class="container">';
  htmlText += "<span ";
  htmlText += 'class="button display-topright"></span>';
  htmlText +=
    '<iframe class="modal-iframe" src=site_url + "/user/cmpny_del_warn.php"></iframe>';
  htmlText += '<div class="button-holder">';
  htmlText +=
    '<button onclick="deleteCompany(' +
    companyId +
    ')" class="btn btn-danger">' +
    lblDelete +
    "</button>&nbsp;";
  htmlText +=
    '<button id="btn-close" onclick="closeDeleteCompanyModal()" class="btn btn-info">' +
    lblClose +
    "</button><div>";
  htmlText += "</div></div></div>";

  $("#divDeleteCompany").html(htmlText).show();
}

function showdeleteuserdialog(userId) {
  Swal.fire({
    title: "Are you sure?",
    text: $("#translation").data("user_js_phrase7"),
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, delete it!",
  }).then(function (result) {
    if (result.value) {
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: { sign: "company_user_delete", user_id: userId },
      }).done(function (data) {
        if (data == "success") {
          window.location.reload();
        } else {
          Command: toastr["error"]("Something is wrong!");

          toastr.options = {
            closeButton: false,
            debug: false,
            newestOnTop: false,
            progressBar: false,
            positionClass: "toast-top-right",
            preventDuplicates: false,
            onclick: null,
            showDuration: 300,
            hideDuration: 1000,
            timeOut: 5000,
            extendedTimeOut: 1000,
            showEasing: "swing",
            hideEasing: "linear",
            showMethod: "fadeIn",
            hideMethod: "fadeOut",
          };
        }
      });
    }
  });
}

function add_company_user() {
  $("#delete_user_content").toggle();
}

//User Signup and validation
$("#add_company_user_btn").click(function (event) {
  event.preventDefault();
  let button = $(this);

  let username_input = $("#signup_username");
  let email_input = $("#signup_email");
  let password_input = $("#signup_password");
  let confirm_password_input = $("#signup_confirm_password");
  let phone_input = $("#signup_phone");
  let company_input = $("#signup_company");
  let approve_per = $("input[name='approve_per']:checked").val();

  let username = username_input.val();
  let email = email_input.val();
  let password = password_input.val();
  let confirm_password = confirm_password_input.val();
  let phone = phone_input.val();
  let company = company_input.val();

  let validation = true;

  if (username.length < 1) {
    username_input.css("border", "1px solid red");
    validation = validation & false;
  } else {
    username_input.css("border", "1px solid white");
  }
  if (email.length < 1 || !validateEmail(email)) {
    email_input.css("border", "1px solid red");
    validation = validation & false;
  } else {
    email_input.css("border", "1px solid white");
  }
  if (password.length < 1 || confirm_password != password) {
    password_input.css("border", "1px solid red");
    validation = validation & false;
  } else {
    password_input.css("border", "1px solid white");
  }
  if (confirm_password.length < 1 || confirm_password != password) {
    confirm_password_input.css("border", "1px solid red");
    validation = validation & false;
  } else {
    confirm_password_input.css("border", "1px solid white");
  }
  if (phone.length < 1) {
    phone_input.css("border", "1px solid red");
    validation = validation & false;
  } else {
    phone_input.css("border", "1px solid white");
  }
  if (company.length < 1) {
    company_input.css("border", "1px solid red");
    validation = validation & false;
  } else {
    company_input.css("border", "1px solid white");
  }

  if (validation) {
    button.append(' <i class="fas fa-spin fa-spinner"></i>');
    //Server request
    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: {
        sign: "company_user_signup",
        user_name: username,
        user_email: email,
        approve_per: approve_per,
        user_password: password,
        user_phone: phone,
        user_company_id: company,
      },
    }).done(function (data) {
      if (data == "success") {
        window.location.reload();
      } else {
        button.find("i").remove();
        Command: toastr["error"](data);

        toastr.options = {
          closeButton: false,
          debug: false,
          newestOnTop: false,
          progressBar: false,
          positionClass: "toast-top-full-width",
          preventDuplicates: false,
          onclick: null,
          showDuration: 300,
          hideDuration: 1000,
          timeOut: 5000,
          extendedTimeOut: 1000,
          showEasing: "swing",
          hideEasing: "linear",
          showMethod: "fadeIn",
          hideMethod: "fadeOut",
        };
      }
    });
  }
});

function closeDeleteCompanyModal() {
  $("#divDeleteCompany").hide();
}

function deleteCompany(company_id) {
  $.ajax({
    url: site_url + "/option_server.php",
    type: "POST",
    data: { sign: "delete_company", company_id: company_id },
  }).done(function (data) {
    if (data == "success") {
      $("#divDeleteCompany").hide();
      window.location.href = site_url + "/user/index.php?route=companies";
    } else {
      alert(data);
    }
  });
}

function hideMethodHead(id) {
  if ($("#chk_method" + id).is(":checked")) {
    $("#method_head" + id).show();
  } else {
    $("#method_head" + id).hide();
  }
}

function showNotes(txtNote) {
  $("#" + txtNote).show();
}

function showMenu() {
  // menu =  document.getElementsByClassName("menu")[0];
  menu = $(".menu");
  menu.toggle(200);
}

function showmoduleoption() {
  $.ajax({
    url: site_url + "/option_server.php",
    type: "POST",
    data: { sign: "get_package_class" },
  }).done(function (data) {
    if (data.length > 0) {
      let packages = JSON.parse(data);
      let options = "";
      for (let key in packages) {
        options += `
                    <option value={"min":"${
                      packages[key]["package_size_min"]
                    }","max":"${packages[key]["package_size_max"]}"}>
                        ${packages[key]["package_size_min"]} - ${
          packages[key]["package_size_max"]
        } ${$("#translation").data("user_js_phrase28")}
                    </option>
                `;
      }
      $("#company_plan_editor_classes").append(options);
    }
  });
}

/* consultant */

$("#question_con_follow_up").change(function (event) {
  let question_follow_up = parseInt($(this).val());
  let question_id = $(this).data("question_id");
  $.ajax({
    url: site_url + "/option_server.php",
    type: "POST",
    data: {
      sign: "update_con_question_follow_up",
      question_id: question_id,
      question_follow_up: question_follow_up,
    },
  }).done(function (data) {
    if (data == "success") {
      window.location.reload();
    } else {
      alert(data);
    }
  });
});
$("#question_con_type").change(function (event) {
  let question_type = $(this).val();
  let question_id = $(this).data("question_id");
  $.ajax({
    url: site_url + "/option_server.php",
    type: "POST",
    data: {
      sign: "update_con_question_type",
      question_id: question_id,
      question_type: question_type,
    },
  }).done(function (data) {
    if (data == "success") {
      window.location.reload();
    } else {
      alert(data);
    }
  });
});
$("#question_con_view").change(function () {
  let company_id = $(this).val();
  if (company_id.length > 0) {
    window.location.href =
      site_url + "/user/index.php?route=consultant&company=" + company_id;
  } else {
    window.location.href = site_url + "/user/index.php?route=consultant";
  }
});
$("#new_con_question_button").click(function (event) {
  event.preventDefault();

  let question_name_input = $("#new_question_name");
  let question_name = question_name_input.val();

  let validation = true;
  if (question_name.length < 1) {
    question_name_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
    validation = validation & false;
  }
  if (validation) {
    question_name_input.css("border-color", "#ced4da");
    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: { sign: "add_con_question", question_name: question_name },
    }).done(function (data) {
      if (data == "success") {
        window.location.reload();
      } else {
        alert(data);
      }
    });
  }
});
//Question res delete
$("#question_con_delete").click(function (evnet) {
  event.preventDefault();
  let question_id = $(this).data("question_id");

  let delete_confirm = confirm($("#translation").data("user_js_phrase12"));
  if (delete_confirm) {
    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: { sign: "delete_con_question", question_id: question_id },
    }).done(function (data) {
      if (data == "success") {
        window.location.href = "/nodg/user/index.php?route=consultant";
      } else alert(data);
    });
  }
});

//question translation update
$(".question-translation-con-save").click(function () {
  let lang_code = $(this).data("lang_code");
  let question_id = $(this).data("question_id");
  let question_name = $(this)
    .parents(".question-single-ctn")
    .find(".question-name")
    .val();
  let question_tips_yes =
    tinymce.editors[
      $(this)
        .parents(".question-single-ctn")
        .find(".question-tips-yes")
        .attr("id")
    ].getContent();
  let question_tips_no =
    tinymce.editors[
      $(this)
        .parents(".question-single-ctn")
        .find(".question-tips-no")
        .attr("id")
    ].getContent();
  let question_option1 = "";
  let question_option2 = "";
  let question_option3 = "";
  let question_option4 = "";
  let question_option5 = "";

  let question_option1_input = $(this)
    .parents(".question-single-ctn")
    .find(".question_option1");
  let question_option2_input = $(this)
    .parents(".question-single-ctn")
    .find(".question_option2");
  let question_option3_input = $(this)
    .parents(".question-single-ctn")
    .find(".question_option3");
  let question_option4_input = $(this)
    .parents(".question-single-ctn")
    .find(".question_option4");
  let question_option5_input = $(this)
    .parents(".question-single-ctn")
    .find(".question_option5");

  if (question_option1_input.length)
    question_option1 = question_option1_input.val();
  if (question_option2_input.length)
    question_option2 = question_option2_input.val();
  if (question_option3_input.length)
    question_option3 = question_option3_input.val();
  if (question_option4_input.length)
    question_option4 = question_option4_input.val();
  if (question_option5_input.length)
    question_option5 = question_option5_input.val();

  $.ajax({
    url: site_url + "/option_server.php",
    type: "POST",
    data: {
      sign: "update_con_translation_question",
      lang_code: lang_code,
      question_id: question_id,
      question_name: question_name,
      question_tips_yes: question_tips_yes,
      question_tips_no: question_tips_no,
      question_option1: question_option1,
      question_option2: question_option2,
      question_option3: question_option3,
      question_option4: question_option4,
      question_option5: question_option5,
    },
  }).done(function (data) {
    if (data == "success") window.location.reload();
    else alert(data);
  });
});
//Question res basic update
$("#question_con_basic").click(function (event) {
  event.preventDefault();
  let question_id = $(this).data("question_id");
  let yes_conponse = $("#question_methods_yes").val().toString();
  let no_conponse = $("#question_methods_no").val().toString();
  let question_activate_tip_yes = $("#question_activate_tip_yes").is(":checked")
    ? 1
    : 0;
  let question_activate_tip_no = $("#question_activate_tip_no").is(":checked")
    ? 1
    : 0;
  let company_id = $(this).data("company_id");
  let question_catetory = "";
  let question_follow_up_yes = 0;
  let question_follow_up_no = 0;

  if ($("#question_category").length)
    question_catetory = $("#question_category").val();
  if ($("#question_follow_up_yes").length)
    question_follow_up_yes = $("#question_follow_up_yes").val();
  if ($("#question_follow_up_no").length)
    question_follow_up_no = $("#question_follow_up_no").val();

  $.ajax({
    url: site_url + "/option_server.php",
    type: "POST",
    data: {
      sign: "update_con_question_basic",
      question_id: question_id,
      category_id: question_catetory,
      yes_conponse: yes_conponse,
      no_conponse: no_conponse,
      question_activate_tip_yes: question_activate_tip_yes,
      question_activate_tip_no: question_activate_tip_no,
      question_follow_up_yes: question_follow_up_yes,
      question_follow_up_no: question_follow_up_no,
      company_id: company_id,
    },
  }).done(function (data) {
    if (data == "success") window.location.reload();
    else alert(data);
  });
});

/* consultant */
/* Responder  */
//Add res question
$("#new_res_question_button").click(function (event) {
  event.preventDefault();

  let question_name_input = $("#new_question_name");
  let question_name = question_name_input.val();

  let validation = true;
  if (question_name.length < 1) {
    question_name_input.css("border-bottom", "3px solid rgba(160, 0, 0, .5)");
    validation = validation & false;
  }
  if (validation) {
    question_name_input.css("border-color", "#ced4da");
    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: { sign: "add_res_question", question_name: question_name },
    }).done(function (data) {
      if (data == "success") {
        window.location.reload();
      } else {
        alert(data);
      }
    });
  }
});

//Question res view update
$("#question_res_view").change(function () {
  let company_id = $(this).val();
  if (company_id.length > 0) {
    window.location.href =
      site_url + "/user/index.php?route=responder&company=" + company_id;
  } else {
    window.location.href = site_url + "/user/index.php?route=responder";
  }
});

$("#question_res_type").change(function (event) {
  let question_type = $(this).val();
  let question_id = $(this).data("question_id");
  $.ajax({
    url: site_url + "/option_server.php",
    type: "POST",
    data: {
      sign: "update_res_question_type",
      question_id: question_id,
      question_type: question_type,
    },
  }).done(function (data) {
    if (data == "success") {
      window.location.reload();
    } else {
      alert(data);
    }
  });
});
$("#question_res_follow_up").change(function (event) {
  let question_follow_up = parseInt($(this).val());
  let question_id = $(this).data("question_id");
  $.ajax({
    url: site_url + "/option_server.php",
    type: "POST",
    data: {
      sign: "update_res_question_follow_up",
      question_id: question_id,
      question_follow_up: question_follow_up,
    },
  }).done(function (data) {
    if (data == "success") {
      window.location.reload();
    } else {
      alert(data);
    }
  });
});

//Question res basic update
$("#question_res_basic").click(function (event) {
  event.preventDefault();
  let question_id = $(this).data("question_id");
  let yes_response = $("#question_methods_yes").val().toString();
  let no_response = $("#question_methods_no").val().toString();
  let question_activate_tip_yes = $("#question_activate_tip_yes").is(":checked")
    ? 1
    : 0;
  let question_activate_tip_no = $("#question_activate_tip_no").is(":checked")
    ? 1
    : 0;
  let company_id = $(this).data("company_id");
  let question_catetory = "";
  let question_follow_up_yes = 0;
  let question_follow_up_no = 0;

  if ($("#question_category").length)
    question_catetory = $("#question_category").val();
  if ($("#question_follow_up_yes").length)
    question_follow_up_yes = $("#question_follow_up_yes").val();
  if ($("#question_follow_up_no").length)
    question_follow_up_no = $("#question_follow_up_no").val();

  $.ajax({
    url: site_url + "/option_server.php",
    type: "POST",
    data: {
      sign: "update_res_question_basic",
      question_id: question_id,
      category_id: question_catetory,
      yes_response: yes_response,
      no_response: no_response,
      question_activate_tip_yes: question_activate_tip_yes,
      question_activate_tip_no: question_activate_tip_no,
      question_follow_up_yes: question_follow_up_yes,
      question_follow_up_no: question_follow_up_no,
      company_id: company_id,
    },
  }).done(function (data) {
    if (data == "success") window.location.reload();
    else alert(data);
  });
});

//Question res delete
$("#question_res_delete").click(function (evnet) {
  event.preventDefault();
  let question_id = $(this).data("question_id");

  let delete_confirm = confirm($("#translation").data("user_js_phrase12"));
  if (delete_confirm) {
    $.ajax({
      url: site_url + "/option_server.php",
      type: "POST",
      data: { sign: "delete_res_question", question_id: question_id },
    }).done(function (data) {
      if (data == "success") {
        window.location.href = "/nodg/user/index.php?route=responder";
      } else alert(data);
    });
  }
});

//question translation update
$(".question-translation-res-save").click(function () {
  let lang_code = $(this).data("lang_code");
  let question_id = $(this).data("question_id");
  let question_name = $(this)
    .parents(".question-single-ctn")
    .find(".question-name")
    .val();
  let question_tips_yes =
    tinymce.editors[
      $(this)
        .parents(".question-single-ctn")
        .find(".question-tips-yes")
        .attr("id")
    ].getContent();
  let question_tips_no =
    tinymce.editors[
      $(this)
        .parents(".question-single-ctn")
        .find(".question-tips-no")
        .attr("id")
    ].getContent();
  let question_option1 = "";
  let question_option2 = "";
  let question_option3 = "";
  let question_option4 = "";
  let question_option5 = "";
  let question_option6 = "";

  let question_option1_input = $(this)
    .parents(".question-single-ctn")
    .find(".question_option1");
  let question_option2_input = $(this)
    .parents(".question-single-ctn")
    .find(".question_option2");
  let question_option3_input = $(this)
    .parents(".question-single-ctn")
    .find(".question_option3");
  let question_option4_input = $(this)
    .parents(".question-single-ctn")
    .find(".question_option4");
  let question_option5_input = $(this)
    .parents(".question-single-ctn")
    .find(".question_option5");
  let question_option6_input = $(this)
    .parents(".question-single-ctn")
    .find(".question_option6");

  if (question_option1_input.length)
    question_option1 = question_option1_input.val();
  if (question_option2_input.length)
    question_option2 = question_option2_input.val();
  if (question_option3_input.length)
    question_option3 = question_option3_input.val();
  if (question_option4_input.length)
    question_option4 = question_option4_input.val();
  if (question_option5_input.length)
    question_option5 = question_option5_input.val();
  if (question_option6_input.length)
    question_option6 = question_option6_input.val();

  $.ajax({
    url: site_url + "/option_server.php",
    type: "POST",
    data: {
      sign: "update_res_translation_question",
      lang_code: lang_code,
      question_id: question_id,
      question_name: question_name,
      question_tips_yes: question_tips_yes,
      question_tips_no: question_tips_no,
      question_option1: question_option1,
      question_option2: question_option2,
      question_option3: question_option3,
      question_option4: question_option4,
      question_option5: question_option5,
      question_option6: question_option6,
    },
  }).done(function (data) {
    if (data == "success") window.location.reload();
    else alert(data);
  });
});

$("#add_report_format").click(function () {
  let report_format_id = $("#report_format").val();
  let report_ticket_id = $("#report_ticket_id").val();
  //alert(report_ticket_id);

  $.ajax({
    url: site_url + "/option_server.php",
    type: "POST",
    data: {
      sign: "set_session_report_format",
      report_format_id: report_format_id,
      report_ticket_id: report_ticket_id,
    },
  }).done(function (data) {
    if (data == "success") {
      window.location.reload();
    } else {
      alert(data);
    }
  });
});

/* Responder  */

$("#submitreportrequest").click(function (event) {
  console.log("hi");
  debugger;
});
$(document).ready(function () {
  $("#draw_graph_1").trigger("click");
  $("#draw_graph_res_1").trigger("click");
});

$(document).ready(function () {
  $(".click_select_report").click(function () {
    $(".select_report").removeClass();
    $("#request_form_id").val($(this).attr("attr-val"));
    $(this).addClass("select_report");
  });
  $("#permission_by_company").prop("checked", true);
  //Create ticket
  $("#request_from_ticket").click(function (event) {
    event.preventDefault();

    let request_form_id = $("#request_form_id").val();
    let permisson_ticket_title = $("#permisson_ticket_title").val();
    let permission_by = $("input[name='permission_by']:checked").val();

    if (permisson_ticket_title != "") {
      $.ajax({
        url: site_url + "/option_server.php",
        type: "POST",
        data: {
          sign: "request_form",
          request_form_id: request_form_id,
          permission_by: permission_by,
          permisson_ticket_title: permisson_ticket_title,
        },
      }).done(function (data) {
        data = JSON.parse(data);
        if (data["status"] == "success") {
          window.location.href =
            site_url + "/user/index.php?route=ticket&page=nticket_success";
        } else {
          alert(data["message"]);
        }
      });
    } else {
      $("#alertSectionemptyptitle").show();
      setTimeout(function () {
        $("#alertSectionemptyptitle").hide();
      }, 3000);
    }
  });

  $("#request_na_ticket").click(function (event) {
    event.preventDefault();

    let request_form_id = $("#request_form_id").val();

    if (request_form_id) {
      window.location.href =
        site_url +
        "/user/index.php?route=ticket&req_id=" +
        request_form_id +
        "&page=summarize";
    } else {
      alert("invalid request.");
    }

    //  $.ajax({
    //         url: site_url + '/option_server.php',
    //         type: 'POST',
    //         data: {'sign': 'request_na_form', 'request_form_id': request_form_id}
    //     }).done(function (data) {
    //         data = JSON.parse(data);
    //         if (data['status'] == 'success') {
    //             window.location.href = site_url + "/user/index.php?route=ticket&req_id="+ data['req_id'] +"&page=summarize";
    //         } else {
    //             alert(data['message']);
    //         }
    //     })
  });
  createDataTable();
 createCategoriesDataTable();

  $('#choose_user_ress').change(function () {
    if ($(this).val() == '0') {
      $('.table-graph .user').show();
      $('.table-graph [class^="responder_"]').show(); // Show all rows with classes starting with "responder_"
    } else if ($(this).val() == '1') {
      $('.table-graph .user').show();
      $('.table-graph [class^="responder_"]').hide(); // Hide all rows with classes starting with "responder_"
    } else if ($(this).val() == '2') {
      $('.table-graph .user').hide();
      $('.table-graph [class^="responder_"]').show(); // Show all rows with classes starting with "responder_"
    }
  });

  $('#choose_user_ress_categories').change(function () {
    if ($(this).val() == '0') {
      $('.table-graph-cat .user').show();
      $('.table-graph-cat [class^="responder_"]').show(); // Show all rows with classes starting with "responder_"
    } else if ($(this).val() == '1') {
      $('.table-graph-cat .user').show();
      $('.table-graph-cat [class^="responder_"]').hide(); // Hide all rows with classes starting with "responder_"
    } else if ($(this).val() == '2') {
      $('.table-graph-cat .user').hide();
      $('.table-graph-cat [class^="responder_"]').show(); // Show all rows with classes starting with "responder_"
    }
  });
  
});


// custom 20-09-2023


function initializeDataTable() {
  // Initialize DataTable on your table
  $('#gtable').DataTable({
    "paging": false,  // Disable pagination
    "searching": false,  // Disable searching
    "info": false,  // Disable "Showing X of Y entries"
});

}
function initializeCategoryDataTable() {
  // Initialize DataTable on your table
  $('#ctable').DataTable({
    "paging": false,  // Disable pagination
    "searching": false,  // Disable searching
    "info": false,  // Disable "Showing X of Y entries"
});

}
function initializeCompanyCategoryDataTable() {
  // Initialize DataTable on your table
  $('#cctable').DataTable({
    "paging": false,  // Disable pagination
    "searching": false,  // Disable searching
    "info": false,  // Disable "Showing X of Y entries"
});

}


// summerise page




const summeriseTimeout = setTimeout(newsummerisepage, 3000);
function newsummerisepage() {
  

  if(window.location.href.includes('&page=summarize')) {
    const sourceElement = $('form .ticket-label');

  if (sourceElement) {
    const selectedText = $(sourceElement[1]).html(); 

    console.log(tinymce);
  
    if (tinymce.activeEditor) {
      tinymce.activeEditor.execCommand("mceInsertContent", false, selectedText);
    } 
  } 
  }
}
