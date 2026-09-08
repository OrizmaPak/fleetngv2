$((function(){"use strict";var r=$(".auth-reset-password-form");r.length&&r.validate({rules:{password:{required:!0},password_confirmation:{required:!0,equalTo:"#reset-password-new"}}})}));
