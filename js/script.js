// window.addEventListener('DOMContentLoaded', event => {

//     // Toggle the side navigation
//     const sidebarToggle = document.body.querySelector('#sidebarToggle');
//     if (sidebarToggle) {
//         // Uncomment Below to persist sidebar toggle between refreshes
//         // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
//         //     document.body.classList.toggle('sb-sidenav-toggled');
//         // }
//         sidebarToggle.addEventListener('click', event => {
//             event.preventDefault();
//             document.body.classList.toggle('sb-sidenav-toggled');
//             localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
//         });
//     }

// });


//Fetch Weather script
async function fetchWeather() {
    const lat = 14.615399129244583;
    const lon = 121.0178811369513;
    const apiUrl = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,relative_humidity_2m,wind_speed_10m,weathercode&daily=temperature_2m_max,relative_humidity_2m_max,wind_speed_10m_max,weathercode&timezone=auto`;

    try {
        const response = await fetch(apiUrl);
        const data = await response.json();
        const weather = data.current;
        const tomorrow = data.daily;

        document.getElementById('temp').innerText = weather.temperature_2m;
        document.getElementById('humidity').innerText = weather.relative_humidity_2m;
        document.getElementById('wind').innerText = (weather.wind_speed_10m * 3.6).toFixed(1);
        
        document.getElementById('tomorrow-temp').innerText = tomorrow.temperature_2m_max[1];
        document.getElementById('tomorrow-humidity').innerText = tomorrow.relative_humidity_2m_max[1];
        document.getElementById('tomorrow-wind').innerText = (tomorrow.wind_speed_10m_max[1] * 3.6).toFixed(1);
        
        const weatherIconsMap = {
            0: "wi-day-sunny",
            1: "wi-day-sunny-overcast",
            2: "wi-day-cloudy",
            3: "wi-cloudy",
            45: "wi-fog",
            48: "wi-fog",
            51: "wi-showers",
            61: "wi-rain",
            63: "wi-rain-wind",
            65: "wi-rain",
            95: "wi-thunderstorm"
        };

        document.getElementById('weather-icon').className = `wi ${weatherIconsMap[weather.weathercode] || "wi-na"} icon`;
        document.getElementById('tomorrow-weather-icon').className = `wi ${weatherIconsMap[tomorrow.weathercode[1]] || "wi-na"} icon`;
    } catch (error) {
        console.error("Error fetching weather data:", error);
    }
}

fetchWeather();
setInterval(fetchWeather, 300000); // Refresh every 5 minutes




document.addEventListener('DOMContentLoaded', function() {
    // Edit button click handler
    document.querySelectorAll('.edit-resident-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Get data from button attributes
            const residentId = this.getAttribute('data-id');
            const residentName = this.getAttribute('data-name');
            const residentAddress = this.getAttribute('data-address');
            const residentPhone = this.getAttribute('data-phone');
            
            // Set values in the edit modal
            document.getElementById('editResidentId').value = residentId;
            document.getElementById('editName').value = residentName;
            document.getElementById('editAddress').value = residentAddress;
            document.getElementById('editPhone').value = residentPhone;
            
            // Show the edit modal
            const editModal = new bootstrap.Modal(document.getElementById('editResidentModal'));
            editModal.show();
        });
    });
});

