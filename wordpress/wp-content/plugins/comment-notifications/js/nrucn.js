jQuery(document).ready(function($) {
    $(".subscribe-btn").on("click", function() {
        var postId = $(this).data("post-id");
        var userEmail = prompt("Please enter your email for notifications:");

        if (userEmail) {
            $.ajax({
                type: "POST",
                url: nrucn_obj.ajax_url,
                data: {
                    action: "nrucn_subscribe",
                    post_id: postId,
                    email: userEmail
                },
                success: function(response) {
                    alert(response.message);
                }
            });
        }
    });
});
