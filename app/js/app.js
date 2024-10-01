
var readAirlineApi = 'http://localhost:3000/app/api/readAirline.php';
var createAirlineApi = 'http://localhost:3000/app/api/createAirline.php';
var updateAirlineApi = 'http://localhost:3000/app/api/updateAirline.php';
var deleteAirlineApi = 'http://localhost:3000/app/api/deleteAirline.php';


function start(){
    getAirline(renderAirline);
    handleCreateAirline();
}

start();

function getAirline(callback){
    fetch(readAirlineApi)
        .then(function(Response){
            return Response.json();
        }).then(callback);

}

function createAirline(data, callback){
    var options = {
        method : 'post',
        headers: {
            "Content-Type": "application/json",
            // 'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: JSON.stringify(data)
    }
    fetch(createAirlineApi, options)
        .then(function(Response){
            Response.json();
        })
        .then(callback);
        Swal.fire(
            'Đã thêm',
            'Bạn đã thêm thành công.',
            'success'
        )
}

function handel_delete_airline(maMB){
    Swal.fire({
        title: 'Bạn đã chắc chắn chưa?',
        text: "Bạn sẽ không còn dữ liệu sau khi xóa",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Tôi đồng ý'
    })
    var options = {
        method : 'delete',
        headers: {
            "Content-Type": "application/json",
            // 'Content-Type': 'application/x-www-form-urlencoded',
          }
    }
    fetch(deleteAirlineApi+ '/' + maMB, options)
        .then(function(Response){
            Response.json();
        })
        .then(function(){
            var airlineItem = document.querySelector('.airline-item-' + maMB);
            if(airlineItem) {
                airlineItem.remove();
            }
        });
}

function handelEditAirline(maMB){
    var options = {
        method : 'put',
        headers: {
            "Content-Type": "application/json",
            // 'Content-Type': 'application/x-www-form-urlencoded',
          },
        body: JSON.stringify(data)
    }
    fetch(updateAirlineApi + "/" + maMB)
}



function renderAirline(airlines){
    var listAirline = document.querySelector('.tbody');
    var htmls = airlines.map(function(airline){
        return `<tr class = "airline-item-${airline.maMB}">
        <td>${airline.tenMayBay}</td>
        <td>${airline.hangMayBay}</td>
        <td>${airline.gheToiDa}</td>
        <td>                           
                            <button class='fix' onclick='edit(${airline.maMB});'><i class='ti-pencil-alt'></i></button>
                            <button class='trash' onclick='delete_airline(${airline.maMB});'><i class='ti-trash'></i></button>
        </td>
        </tr>`;
    });
    listAirline.innerHTML = htmls.join('');
}

function handleCreateAirline(){
    var createBtn = document.querySelector('#save');

    createBtn.onclick = function(){
        var tenMayBay = document.querySelector('input[name="tenMayBay"]');
        var hangMayBay = document.querySelector('input[name="hangMayBay"]');
        var gheToiDa = document.querySelector('input[name="gheToiDa"]');

        var formData = {
            tenMayBay: tenMayBay,
            hangMayBay: hangMayBay,
            gheToiDa: gheToiDa
        }
        createAirline(formData, function(){
            getAirline(renderAirline);
        });
    }
}