// Jsonize.cpp : Ce fichier contient la fonction 'main'. L'exécution du programme commence et se termine à cet endroit.
//

#include <iostream>
#include <fstream>
#include <vector>
using namespace std;


int main()
{
	std::ifstream input("data_sensors.json", std::ios::binary);
	std::vector<char> s(std::istreambuf_iterator<char>(input), {});

	string ans;

	for (int i = 0; i < s.size(); i++) {
		if (s[i] == '\'') {
			ans += "\\\\'";
		}
		else {
			ans += s[i];
		}
	}

	std::fstream fs;
	fs.open("datajson_esc.json", std::fstream::out | std::fstream::trunc);

	fs << ans;

	fs.close();

}

