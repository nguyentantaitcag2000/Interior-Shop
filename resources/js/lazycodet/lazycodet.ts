import Swal from 'sweetalert2';
import loadingGif from './loading.gif';
import './lazycodet.css';
interface AlertProcessingParams {
    requireConfirm?: boolean;
    alertMessage?: string;
    workerFunction: () => Promise<any>; // hoặc kiểu hàm khác bạn mong muốn
    alertTitle?: string;
    processMessage?: string;
    processTitle?: string;
    confirmButtonText?: string;
}
export const LazyCodet = {
    AlertSuccess: function(msg:string)
    {
        /*
        Bắt buộc phải delay để khi dùng hàm AlertProcessing sẽ ưu tiên đẻ cho Swal.close trước, thì khi
        lấy ra sử dụng người dùng cho truyền vào workerFunction là 1 hàm alertSuccess/Error khác sẽ không
        bị thực hiện cùng với Swal.close dẫn đến lỗi hiển thị
        */
        
        setTimeout(()=>{
            Swal.fire({
                icon: 'success',
                title: msg,
                showConfirmButton: false,
                timer: 1500
                });
        },100);
        
    },
    AlertError: function(msg:string)
    {
        /*
        Bắt buộc phải delay để khi dùng hàm AlertProcessing sẽ ưu tiên đẻ cho Swal.close trước, thì khi
        lấy ra sử dụng người dùng cho truyền vào workerFunction là 1 hàm alertSuccess/Error khác sẽ không
        bị thực hiện cùng với Swal.close dẫn đến lỗi hiển thị
        */
        setTimeout(()=>{
            Swal.fire({
                icon: 'error',
                title: msg,
                showConfirmButton: true,
            });
        },100);
        
    },
    
    AlertProcessing: async function(params: AlertProcessingParams) {
        const {
            requireConfirm = true,
            alertMessage = 'Are you want to do it', 
            workerFunction, 
            alertTitle = "Are you sure?", 
            processMessage = 'Waiting...',
            processTitle = "Processing...",
            confirmButtonText = 'Yes !'
        } = params;

        try {
            let swallResult:any = null;
            if(requireConfirm)
            {
                swallResult = await Swal.fire({
                    title: alertTitle,
                    text: alertMessage,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: confirmButtonText
                });
            }
            if ( requireConfirm == false || swallResult.isConfirmed) {
                Swal.fire({
                    title: processTitle,
                    text: processMessage,
                    imageUrl:  loadingGif,
                    showConfirmButton: false,
                    allowOutsideClick: false
                });
    
                await workerFunction();
                Swal.close();
            }
        } catch (error) {
            console.error(error);
        }
    }
    
}
export const LazyConvert = {
    ToMoney: (amount:number|undefined) => {
        if(typeof amount == 'undefined')
            amount = -1;
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
    }
}
export const LazyCheck = {
    ValidUsername: (username: string) =>
    {
      var isValid = true;
      var notify_username = document.getElementById('notify_username') as HTMLElement;
      notify_username.innerHTML=''; 
      var p = document.createElement('p');

      if(username.length<6)
      {
        isValid = false;
        p.innerHTML = "Username must be equal to or more than 6 characters";
        p.classList.remove('valid');
        p.classList.add('not-valid');
      }
      notify_username.appendChild(p);
      return isValid;
    },
    ValidEmail: (email:string,language='vie') => {
      let emailValidityMessage = "Email's valid.";

      let translatedEmailValidityMessage = "";
      let _translatedEmailValidityMessage = "";

      if (language === "vie") {
        translatedEmailValidityMessage = "Email hợp lệ.";
        _translatedEmailValidityMessage = "Email không hợp lệ.";
      } else {
        translatedEmailValidityMessage = emailValidityMessage;
        _translatedEmailValidityMessage = "Email's not valid.";
      }
          var isValid = email.match(
        /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
      );
          var notify_email = document.getElementById('notify_email');
          if(notify_email == null)
            throw('notify_email is null');
          notify_email.innerHTML=''; 
          var p = document.createElement('p');
          if(isValid){
            p.innerHTML = translatedEmailValidityMessage;
            p.classList.remove('npt-valid');
            p.classList.add('valid');
          }
          else
          {
            p.innerHTML = _translatedEmailValidityMessage;
            p.classList.remove('valid');
            p.classList.add('not-valid');
          }
          notify_email.appendChild(p);
          return isValid;
    },
    PasswordSecure: (password:string,language='vie') =>
    {
      let passwordRequirements = {
        containsNumber: "The password must contain at least one number [0-9]",
        containsUppercase: "The password must contain at least one uppercase character [A-Z]",
        containsLowercase: "The password must contain at least one lowercase character [a-z]",
        minLength: "The password must be 8 characters or longer",
        containsSpecialChar: "The password must contain at least one special character [!@#$%^&*]"
      };

      let translatedPasswordRequirements = passwordRequirements;

      if (language === "vie") {
        translatedPasswordRequirements = {
          containsNumber: "Mật khẩu phải chứa ít nhất một chữ số [0-9]",
          containsUppercase: "Mật khẩu phải chứa ít nhất một ký tự hoa [A-Z]",
          containsLowercase: "Mật khẩu phải chứa ít nhất một ký tự thường [a-z]",
          minLength: "Mật khẩu phải có ít nhất 8 ký tự",
          containsSpecialChar: "Mật khẩu phải chứa ít nhất một ký tự đặc biệt [!@#$%^&*]"
        };
      }
      var isValid = true;
      var el = document.getElementById('notify_password') as HTMLElement;
      el.innerHTML = '';
      var p = document.createElement('p');
      if(!/(?=.*[0-9])/gm.test(password))
      { 
          isValid = false;
          p.innerHTML = translatedPasswordRequirements.containsNumber;
          p.classList.remove('valid');
          p.classList.add('not-valid');
      }
      else
      {
          p.innerHTML =  translatedPasswordRequirements.containsNumber;
          p.classList.remove('not-valid');
          p.classList.add('valid');
      }
      el.appendChild(p);
      p = document.createElement('p');
      if(!/(?=.*[A-Z])/gm.test(password))
      {
          isValid = false;

          p.innerHTML =  translatedPasswordRequirements.containsUppercase;
          p.classList.remove('valid');
          p.classList.add('not-valid');
      }
      else
      {
          p.innerHTML =  translatedPasswordRequirements.containsUppercase;
          p.classList.remove('not-valid');
          p.classList.add('valid');
      }
      el.appendChild(p);
      p = document.createElement('p');
      if(!/(?=.*[a-z])/gm.test(password))
      {
          isValid = false;

          p.innerHTML =  translatedPasswordRequirements.containsLowercase;
          p.classList.remove('valid');
          p.classList.add('not-valid');
      }
      else
      {

          p.innerHTML = translatedPasswordRequirements.containsLowercase;
          p.classList.remove('not-valid');
          p.classList.add('valid');
      }
      el.appendChild(p);
      p = document.createElement('p');
      if(!/(?=.{8,})/gm.test(password))
      {
          isValid = false;

          p.innerHTML = translatedPasswordRequirements.minLength;
          p.classList.remove('valid');
          p.classList.add('not-valid');
      }
      else
      {

          p.innerHTML = translatedPasswordRequirements.minLength;
          p.classList.remove('not-valid');
          p.classList.add('valid');
      }
      el.appendChild(p);
      p = document.createElement('p');
      if(!/(?=.*[!@#\$%\^&\*])/gm.test(password))
      {
          p.innerHTML = translatedPasswordRequirements.containsSpecialChar;
          p.classList.remove('valid');
          p.classList.add('not-valid');
      }
      else
      {
          p.innerHTML = translatedPasswordRequirements.containsSpecialChar;
          p.classList.remove('not-valid');
          p.classList.add('valid');
      }
      el.appendChild(p);
      return isValid;
      // Password RegEx  Meaning
      // ^ The password starts
      // (?=.*[a-z]) The password must contain at least one lowercase character
      // (?=.*[A-Z]) The password must contain at least one uppercase character
      // (?=.*[0-9]) The password must contain at least one number
      // (?=.*[!@#$%^&*])  The password must contain at least one special character.
      // (?=.{8,}) The password must be eight characters or longer
    } 
  
}
