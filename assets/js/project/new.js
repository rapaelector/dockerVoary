// function about new project
$(document).ready(function(){
    $(".select2-selection").css("padding-bottom", 26)
    $("#project_contactSelect").change((e)=>{
        //check if value exist and not empty
        const value = e.target.value;
        if (value && value != null) {
            // then should be desabled if contact is exist
            $('.contact-form-to-check :input').attr('disabled', true).addClass("disabled");
            // then made some ajax there
            $.ajax({
                url: "/user/get/"+value,
                method: "GET",
                accepts: "application/json; charset=utf-8",
                success: (value) => {
                    let object = JSON.parse(value);
                    // then fill all input into the contact form
                    for (let [key, val] of Object.entries(object)) {
                        $("input[name='project[contact]["+key+"]']").val(val);
                    }
                }
            });
        } else {
            $('.contact-form-to-check :input').attr('disabled', false).removeClass("disabled").val("");
        }
    })
})
