import Swal from 'sweetalert2';
import loadingGif from './loading.gif';
export const LazyCodet = {
    AlertSuccess: function(msg)
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
    AlertError: function(msg)
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
    
    AlertProcessing: async function({
        requireConfirm = true,
        alertMessage, 
        workerFunction, 
        alertTitle = "Are you sure?", 
        processMessage = 'Waiting...',
        processTitle = "Processing...",
        confirmButtonText = 'Yes !'
    }) {
        try {
            if(requireConfirm)
            {
                var result = await Swal.fire({
                    title: alertTitle,
                    text: alertMessage,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: confirmButtonText
                });
            }
            if ( requireConfirm == false || result.isConfirmed) {
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