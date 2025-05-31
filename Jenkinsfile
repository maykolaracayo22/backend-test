pipeline {
    agent any  // No necesitas Docker con Composer porque ya tienes vendor/ en el repo

    environment {
        SONARQUBE = 'sonarqube'  // Configuración SonarQube
    }

    stages {
        stage('Clone') {
            steps {
                git branch: 'main',
                    credentialsId: 'githubtoken1',
                    url: 'https://github.com/maykolaracayo22/backend-test.git'
            }
        }

        stage('Install Dependencies') {
            steps {
                echo 'Saltando instalación de dependencias porque vendor/ está en el repo'
            }
        }

        stage('Run Tests') {
            steps {
                sh 'cd reservasback && ./vendor/bin/phpunit --configuration phpunit.xml'
            }
        }

        stage('SonarQube Analysis') {
            environment {
                scannerHome = tool 'SonarQubeScanner'
            }
            steps {
                withSonarQubeEnv(SONARQUBE) {
                    sh """
                    cd reservasback && \\
                    ${scannerHome}/bin/sonar-scanner \\
                    -Dsonar.projectKey=backend-test \\
                    -Dsonar.sources=app,routes,database \\
                    -Dsonar.php.coverage.reportPaths=storage/logs/clover.xml \\
                    -Dsonar.host.url=http://tu_sonarqube_url:9000
                    """
                }
            }
        }

        stage('Quality Gate') {
            steps {
                waitForQualityGate abortPipeline: true
            }
        }

        stage('Deploy') {
            steps {
                echo 'Aquí puedes agregar los comandos para desplegar tu aplicación Laravel.'
            }
        }
    }
}
