//locationData.js

export const PROVINCES = [
    { id: 1, name: "Bengo" },
    { id: 2, name: "Benguela" },
    { id: 3, name: "Bié" },
    { id: 4, name: "Cabinda" },
    { id: 5, name: "Cuando Cubango" },
    { id: 6, name: "Cuanza Norte" },
    { id: 7, name: "Cuanza Sul" },
    { id: 8, name: "Cunene" },
    { id: 9, name: "Huambo" },
    { id: 10, name: "Huíla" },
    { id: 11, name: "Luanda" },
    { id: 12, name: "Lunda Norte" },
    { id: 13, name: "Lunda Sul" },
    { id: 14, name: "Malanje" },
    { id: 15, name: "Moxico" },
    { id: 16, name: "Namibe" },
    { id: 17, name: "Uíge" },
    { id: 18, name: "Zaire" }
  ];
  
  export const MUNICIPALITIES = {
    1: ["Ambriz", "Bula Atumba", "Dande", "Nambuangongo", "Pango Aluquém"],
    2: ["Baía Farta", "Balombo", "Benguela", "Bocoio", "Caimbambo", "Catumbela", "Chongoroi", "Cubal", "Ganda", "Lobito"],
    3: ["Andulo", "Camacupa", "Catabola", "Chinguar", "Cuemba", "Cunhinga", "Nharea"],
    4: ["Belize", "Buco-Zau", "Cabinda", "Cacongo"],
    5: ["Calai", "Cuangar", "Cuchi", "Cuito Cuanavale", "Dirico", "Mavinga", "Menongue", "Rivungo"],
    6: ["Calai", "Cuangar", "Cuchi", "Cuito Cuanavale", "Dirico", "Mavinga", "Menongue", "Rivungo"],
    7: ["Amboim", "Cassongue", "Conda", "Ebo", "Libolo", "Mussende", "Porto Amboim", "Quibala", "Quilenda", "Seles", "Sumbe"],
    8: ["Cahama", "Cuanhama", "Curoca", "Cuvelai", "Namacunde", "Ombadja"],
    9: ["Bailundo", "Caála", "Catchiungo", "Chicala-Cholohanga", "Ecunha", "Huambo", "Londuimbali", "Longonjo", "Mungo", "Tchicala Tcholoanga", "Ucuma"],
    10: ["Caconda", "Caluquembe", "Chiange", "Chibia", "Chicomba", "Chipindo", "Cuvango", "Humpata", "Jamba", "Lubango", "Matala", "Quipungo"],
    11: ["Belas", "Cacuaco", "Cazenga", "Ícolo e Bengo", "Kilamba Kiaxi", "Luanda", "Quiçama", "Talatona", "Viana"],
    12: ["Cambulo", "Capenda Camulemba", "Caungula", "Chitato", "Cuango", "Cuílo", "Lubalo", "Xá-Muteba"],
    13: ["Cacolo", "Dala", "Muconda", "Saurimo"],
    14: ["Cacuso", "Calandula", "Cambundi-Catembo", "Cangandala", "Caombo", "Cuaba Nzogo", "Cunda-Dia-Baze", "Luquembo", "Malanje", "Marimba", "Massango", "Mucari", "Quela"],
    15: ["Alto Zambeze", "Bundas", "Camanongue", "Léua", "Luau", "Luacano", "Luchazes", "Moxico"],
    16: ["Bibala", "Camucuio", "Moçâmedes", "Tômbwa", "Virei"],
    17: ["Ambuila", "Bembe", "Buengas", "Bungo", "Damba", "Macocola", "Mucaba", "Negage", "Puri", "Quimbele", "Quitexe", "Sanza Pombo", "Songo", "Uíge", "Zombo"],
    18: ["Cuimba", "Mbanza Congo", "Nóqui", "Nzeto", "Soyo", "Tomboco"]
  };
  
  export const getMunicipalities = (provinceId) => {
    if (!provinceId) return [];
    return MUNICIPALITIES[provinceId] || [];
  };