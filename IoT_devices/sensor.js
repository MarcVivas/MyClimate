
const mqtt = require('mqtt');
const fetch = (...args) => import('node-fetch').then(({default: fetch}) => fetch(...args));
main();




async function main() {

    // -----------------------------------------------------------
    // Connect to the message broker
    const client = mqtt.connect(
        {
            host: 'mosquitto',
            port: 1883
        }
    );
    // -----------------------------------------------------------

    // -----------------------------------------------------------
    // Read csv
    let collectedData = (require("fs").readFileSync("./data2.csv", "utf8")).split("\r");
    // Remove the column name (Temperature) from the array
    collectedData.shift();
    // -----------------------------------------------------------




    // Once connected ...
    client.on('connect', async () => {
        let apiData = await connectToMyClimateAPI();
        console.log(apiData);

        // Sensor connected!
        console.log('Sensor ' +  process.env.HOSTNAME + ' is now connected to the MQTT broker');

        publishCollectedData(collectedData, 'temperature', client, apiData.sensor.id, apiData.token);

    });


}

/**
 * Every 10 seconds publish a message to the temperature topic!
 * @param collectedData
 * @param topic
 * @param client
 * @param sensorId
 * @param token
 * @return void
 */
function publishCollectedData(collectedData, topic, client, sensorId, token){

    let i = 0; // Counter to iterate the collected data

    setInterval(async () => {
        // Check if the data exists in the server
        await getUserHomes(token);  // If the token, is not valid the sensor will reset.

        let temperatureId = i++ % collectedData.length;

        const message = JSON.stringify({
            "sensorId": sensorId,
            "temperature": collectedData[temperatureId],
            'measured_at': new Date().toISOString().replace(/T/, ' ').replace(/\..+/, ''),
            'token': token
        });

        client.publish(topic, message, {qos: 0}, (error) => {
            if (error) {
                console.error(error);
            } else {
                console.log("Sensor " + sensorId + " has now sent a message to the server!");
            }
        });

    }, 10 * 1000);
}


/**
 * Register || Login to MyClimateAPI
 * @return {Promise<{sensor, token, home}>}
 */
async function connectToMyClimateAPI() {
    const response = await registerUser(process.env.HOSTNAME, '1234');

    let token, home, sensor;
    if (response.ok) {
        // User just registered
        token = (await response.json()).data.token;

        // Create a home with a sensor
        home = (await (await createHome('Mansion', 'House with barbecue!', 'Watermelon street 2', token)).json()).data;
        sensor = (await (await createSensor(home.id, 'Kitchen', token)).json()).data;
    } else {
        // User is already registered
        // Login
        token = (await (await loginUser(process.env.HOSTNAME, '1234')).json()).data.token;

        // Get user's homes
        let homes = (await (await getUserHomes(token)).json()).data;

        console.log(Object.keys(homes));
        if (Object.keys(homes).length === 0) {
            // User does not have homes
            // Create one
            home = (await (await createHome('Mansion', 'House with barbecue!', 'Watermelon street 2', token)).json()).data;
        } else {
            // User has homes
            // Get the first
            home = homes['0'];
        }

        // If sensor has not been created
        let sensors = (await (await getSensorsOfAHome(token, home.id)).json()).data;
        console.log(Object.keys(sensors));

        if (Object.keys(sensors).length === 0) {
            // User does not have sensors
            // Create one
            sensor = (await (await createSensor(home.id, 'Kitchen', token)).json()).data;
        } else {
            // User has sensors
            // Get the first
            sensor = sensors['0'];
        }
    }

    return {
        'token': token,
        'sensor': sensor,
        'home': home
    };

}

/**
 * Log in to MyClimateAPI
 * @param username
 * @param password
 * @return {Promise<unknown>}
 */
async function loginUser(username, password) {
    let response = await fetch('http://MyClimateAPI:8000/user/login', {
        method: 'post',
        body: JSON.stringify({
            'username': username,
            'password': password
        }),
        headers: {'Content-Type': 'application/json'}
    });

    return response.ok ? response: process.exit(1);
}

/**
 * Register to MyClimateAPI
 * @param username
 * @param password
 * @return {Promise<unknown>}
 */
async function registerUser(username, password) {
    return await fetch('http://MyClimateAPI:8000/user/register', {
        method: 'post',
        body: JSON.stringify({
            'username': username,
            'password': password
        }),
        headers: {'Content-Type': 'application/json'}
    });
}

/**
 * Create a new home using MyClimateAPI
 * @param name
 * @param description
 * @param address
 * @param token
 * @return {Promise<unknown>}
 */
async function createHome(name, description, address, token) {
    let response = await fetch('http://MyClimateAPI:8000/homes', {
        method: 'post',
        body: JSON.stringify({
            'name': name,
            'description': description,
            'address': address
        }),
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        }
    });
    return response.ok ? response: process.exit(1);
}

/**
 * Get a user's homes using MyClimateAPI
 * @param token
 * @return {Promise<unknown>}
 */
async function getUserHomes(token) {
    let response =  await fetch('http://MyClimateAPI:8000/user/homes', {
        method: 'get',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        }
    });
    return response.ok ? response: process.exit(1);

}


/**
 * Get a user's sensors using MyClimateAPI
 * @param token
 * @param home_id
 * @return {Promise<unknown>}
 */
async function getSensorsOfAHome(token, home_id) {
    let response =  await fetch('http://MyClimateAPI:8000/homes' + '/' + home_id + '/sensors',  {
        method: 'get',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        }
    });
    return response.ok ? response: process.exit(1);

}

/**
 * Create a new sensor using MyClimateAPI
 * @param home_id
 * @param room
 * @param token
 * @return {Promise<unknown>}
 */
async function createSensor(home_id, room, token){
    let response =  await fetch('http://MyClimateAPI:8000/homes' + '/' + home_id + '/sensors', {
        method: 'post',
        body: JSON.stringify({
            'room': room,
        }),
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        }
    });

    return response.ok ? response: process.exit(1);
}
