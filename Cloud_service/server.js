const mqtt = require('mqtt')
const kafka = require("kafka-node");
const fetch = (...args) => import('node-fetch').then(({default: fetch}) => fetch(...args));



main();

/**
 * Main function
 * @return void
 */
function main(){

    //================================================================
    // Set up

    // Connect to the mqtt broker
    const mqttClient = mqtt.connect(
        {
            host: 'mosquitto',
            port: 1883
        }
    );

    // Connect to Kafka
    const kafkaClient = new kafka.KafkaClient({kafkaHost: "kafka:9092"});
    //================================================================

    // Once connected...
    mqttClient.on('connect', function (){
        // Connected!
        console.log("The server is now connected to the MQTT broker!");

        // Subscribe to the temperature topic
        mqttClient.subscribe('temperature', subscribeCallback);

        const kafkaClientForTheProducer = new kafka.KafkaClient({kafkaHost: "kafka:9092"});
        const kafkaProducer = new kafka.Producer(kafkaClientForTheProducer);

        // Listen to the broker
        mqttClient.on('message', processMQTTMessage(kafkaProducer));

    });

    // Once connected and ready...
    kafkaClient.on('ready', () => {
        console.log("The server is now waiting for the results of the analysis!");
        const kafkaConsumer = new kafka.Consumer(kafkaClient, [{topic: 'analytics_results'}]);
        kafkaConsumer.on('message', processKafkaMessage);
    });



}

/**
 * The returned function is executed when the server receives a new message from the MQTT broker.
 * Saves the received data in the database.
 * Sends the received data to the analytics module.
 * @param kafkaProducer
 * @return function
 */
function processMQTTMessage(kafkaProducer){
    return async (topic, message) => {
        // Message received :)

        // Get json
        const data = JSON.parse(message.toString());

        console.log("Server: Message received from sensor " + data.sensorId);

        // Store sensor's collected data
        let temperature = (await (await storeTemperature(data)).json()).data;


        // Send the received data to the analytics module
        kafkaProducer.send([{
            topic: 'analytics',
            messages: JSON.stringify({
                v: data.temperature,
                ts: data.measured_at,
                token: data.token,
                temperatureId: temperature.id
            })
        }], sendCallback(data.sensorId));


    };
}


/**
 * This function is executed when the server receives a new message from the Kafka broker.
 * Saves the data received in the database.
 * @param message
 */
async function processKafkaMessage(message) {
    message = JSON.parse(JSON.parse(message.value))['0'];
    console.log("Server: Message received from the analytics module!");

    // Store prediction!
    await storePrediction(message);


}


/**
 * MQTT subscribe callback function.
 * @param error
 * @param content
 */
function subscribeCallback(error, content){
    if(error){
        console.error(error);
    }
    else{
        console.log(`The server is now subscribed to the temperature topic!`);
    }
}



/**
 * Kafka send callback function
 * @param sensorId
 * @return {(function(*, *): void)|*}
 */
function sendCallback(sensorId){
    return (error, result) => {
        if(error){
            console.error(error);
        }
        else{
            console.log('The server has now sent data from the sensor ' + sensorId + ' to the analytics module!' )
        }
    };
}

/**
 * Stores a new temperature using MyClimateAPI
 */
async function storeTemperature(data){
    let response = await fetch('http://MyClimateAPI:8000/sensors/' + data.sensorId + '/temperatures', {
        method: 'post',
        body: JSON.stringify({
            'temperature': data.temperature,
            'measured_at': data.measured_at,
        }),
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + data.token
        }
    });
    return response.ok ? response: process.exit(1);
}


/**
 * Stores a new prediction using MyClimateAPI
 */
async function storePrediction(data){
    let response = await fetch('http://MyClimateAPI:8000/temperatures/' + data.temperatureId + '/predictions', {
        method: 'post',
        body: JSON.stringify({
            'y_hat': data.yhat,
            'y_hat_lower': data.yhat_lower,
            'y_hat_upper': data.yhat_upper,
            'date': data.ds
        }),
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + data.token
        }
    });
    return response.ok ? response: process.exit(1);
}



